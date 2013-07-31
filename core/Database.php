<?php
// @author  : atibus
// @date    : 07/15/2013
// @desc    : Firebird Database Class;

class Database
{

    private $instance;
    private $connected;
    private $db_trans;
    private $prepared_query;
    private $query_result;

// database credentials    
    private $dbkey;
    private $driver;
    private $database;
    private $username;
    private $password;
    private $charset;
    private $buffers;
    private $dialect;
    private $role;
    private $port;
    private $host;

    public $_db_errcode;
    public $_db_errmsg;


    public function  __construct($db_key)
    {
        $this->connected = false;
        $this->dbkey = $db_key;
        $this->connect();
    }

    //modified by atibus fix unset configuration error.. undeclare variable kunkunana nukua.
    public function init()
    {
        
        if(!is_file(DB_CONFIG))
        {
            throw new RuntimeException('Database config not found. Please make sure you configured config properly.',0);
            return false;
        }

        $db_config = parse_ini_file(DB_CONFIG,true);
        
        $use_db_config = $db_config[$this->dbkey];

        $this->driver   = isset( $use_db_config['driver'])      ? $use_db_config['driver']     : null; 
        $this->database = isset( $use_db_config['database'])    ? $use_db_config['database']   : null;
        $this->username = isset( $use_db_config['user'])        ? $use_db_config['user']       : null;
        $this->password = isset( $use_db_config['password'])    ? $use_db_config['password']   : null;
        $this->charset  = isset( $use_db_config['charset'])     ? $use_db_config['charset']    : null;
        $this->buffers  = isset( $use_db_config['buffer'])      ? $use_db_config['buffer']     : null;
        $this->dialect  = isset( $use_db_config['dialect'])     ? $use_db_config['dialect']    : null;
        $this->role     = isset( $use_db_config['userrole'])    ? $use_db_config['userrole']   : null;
        $this->port     = isset( $use_db_config['port'])        ? $use_db_config['port']       : null;
        $this->host     = isset( $use_db_config['host'])        ? $use_db_config['host']       : null;

        return true;
    }

    public function connect()
    {
        if(!$this->instance)
        {
            if($this->init())
            {
                if($this->driver == "Firebird")
                {
                    $this->instance = ibase_connect
                    (
                        $this->host . "/" . $this->port . ":" . $this->database,
                        $this->username, $this->password, $this->charset,
                        $this->buffers, $this->dialect, $this->role
                    );
                    if(ibase_errcode())
                    {
                        throw new RuntimeException("Cannot connect to database.");
                    }
                }
                else if($this->driver == "MySQL")
                {
                    throw new RuntimeException("MySQL not yet supported.", 1);
                }
                else
                {
                    throw new RuntimeException("Unknown Database Driver.", 1);   
                }
            }
        }
    }

    public function checkQuery($sql_statement)
    {
        if(!$sql_statement)
        {
            throw new RuntimeException(ibase_errmsg(), ibase_errcode());
        }
    }

    public function setTrans($new_db_trans = IBASE_DEFAULT)
    {
        $this->db_trans = ibase_trans($new_db_trans, $this->instance);
    }

    public function getTrans()
    {
        return $this->db_trans;
    }

    public function prepare($ibase_query)
    {
        $this->prepared_query = ibase_prepare($this->db_trans, $ibase_query);
        $this->checkQuery($this->prepared_query);
    }


    public function execute($data=false)
    {
        if($data and !is_array($data))
        {
            $this->query_result = ibase_execute($this->prepared_query, $data); 

            if(!$this->query_result)
            {
                throw new RuntimeException(ibase_errmsg(), ibase_errcode());
            }

            $this->checkQuery($this->query_result);
        }
        else if(is_array($data))
        {
            $params = array();
            foreach($data as $key => $value)
            {
                $params[$key] = '$data['.$key.']';
            }

            eval('$this->query_result=ibase_execute($this->prepared_query,'.join(',',$params).');'); 

            $this->checkQuery($this->query_result);
            
        }
        else
        {
            $this->query_result = ibase_execute($this->prepared_query);
            $this->checkQuery($this->query_result);
        }
        
    }


    public function gen_id($generator, $incrementer=0)
    {
        return ibase_gen_id($generator,$incrementer);
    }

    public function fetchObject()
    {
        $ibase_object_arr = array();

        while($object = ibase_fetch_object($this->query_result))
        {
            $ibase_object_arr[] = $object;
        }

        return $ibase_object_arr;
    }

    public function fetchArray()
    {
        $ibase_row_arr = array();

        while($row = ibase_fetch_assoc($this->query_result))
        {
            $ibase_row_arr[] = $row;
        }

        return $ibase_row_arr;
    }

    public function isConnected()
    {
        return ($this->instance) ? true : false;
    }

    public function commitTrans()
    {
        ibase_commit($this->db_trans);
    }

    public function rollbackTrans()
    {
        ibase_rollback($this->db_trans);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function __destruct()
    {
        ;
    }
}
?>
