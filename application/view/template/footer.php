


            <div class="row-fluid footer" id="footer">
                <p class="copyright"><center>&copy;Siy Cha Group of Companies. All right reserved. 2013</center></p>
            </div>
        </div>


        <script src="./libraries/jquery/jquery-1.10.2.min.js"></script>
        

        <!-- reset extjs scope css fixed conflicting in bootstrap -->
        <script type="text/javascript">Ext = {buildSettings:{"scopeResetCSS":true}};</script>
        <script src="./libraries/ext-4/ext-all.js"></script>
        <script type="text/javascript" src="./scripts/js/common.js"></script>

        <?php 
            if($this->loaded_js)
            {
                foreach($this->loaded_js as $dynamic_js)
                {
                    echo $dynamic_js;
                }
            }
        ?>

        <!-- BOOTSTRAP -->
        <script src="./libraries/bootstrap/js/bootstrap.min.js"></script>
        <!--[if lt IE 9]>
        <script src="./libraries/bootstrap/js/html5shiv.js"></script>
        <![endif]-->
        
    </body>

</html>