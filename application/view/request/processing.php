<div class="row-fluid" id="trans-container">
	<div class="row">
	    <div class="span3 offset1" id="trans-title"><p class="btn-large btn-success"><?=$this->form_title?></p></div>
	</div>
	<div class="row">&nbsp;</div>
	<div class="row">
	    <div class="span10 offset1" id="trans-container">
	    	<div id="request-details-container"></div>
	        <div id="filter-container"></div>
	        <div id="request-container"></div>
	        <div id="history-container"></div>
	    </div>
	</div>
</div>
<?php $this->view('user/hiddeninfo'); ?>