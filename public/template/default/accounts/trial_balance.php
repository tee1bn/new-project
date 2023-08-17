<?php include 'inc/headers.php';?>



	<div class="container">
		<div class="row">

			<div id="customise_report" class="modal fade " style="display: ;" role="dialog">
							<div class="modal-dialog">

							    <!-- Modal content-->
							    <div class="modal-content">
							      <div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal">&times;</button>
							        <h4 class="modal-title">Customise Report</h4>
							      </div>
							      <div class="modal-body">
							      	<form id="customise_report"
							      	 action="<?=domain;?>/trial-balance/customise_trial_balance_report" method="post">  
							      	
							      		<div class="form-group">
							      			<label>Date</label>
							      			<input required="" type="date" value="<?=$as_of_date;?>" name="as_of_date" class="form-control"> 
							      		</div>
<!-- 

							      		<div class="form-group">
							      			<label>Per Page</label>
							      			<input type="number" value="<?=$per_page;?>" name="per_page" class="form-control"> 
							      			
							      		</div> -->


									      <div class="modal-footer">
									        <button type="submit" class="btn btn-danger" >Submit</button>
									        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									      </div>
							      	</form>
								  </div>
							  	</div>
							</div>
						</div>
			
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="btn-group">
						  <div class="btn-group">
						    <button type="button"  data-toggle="modal" data-target="#customise_report"  class="btn btn-default">
						   <i class="fa fa-cog"></i> Customize Report </button>
<!-- 						    <ul class="dropdown-menu" role="menu">
						      <li><a href="#">Tablet</a></li>
						      <li><a href="#">Smartphone</a></li>
						    </ul>
 -->						  </div>
						</div>

				</div>
				<div class="col-md-6 text-right">
					<div class="btn-group">
						  <button onclick="print();" type="button" class="btn btn-default"> <i class="fa fa-print"></i> &nbsp;</button>
						  <div class="btn-group">
						    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						    Export As <span class="caret"></span></button>
						    <ul class="dropdown-menu" role="menu">
						      <li><a href="#">CSV</a></li>
						      <li><a href="#">PDF</a></li>
						    </ul>
						  </div>
						</div>

				</div>
			</div>
			<div class="col-md-12">
			<hr>
				<h5 class="text-center"><?=$this->auth()->company->name;?></h5>
				<h3 class="text-center">Trial Balance</h3>
				<h5 class="text-center">As of <?=date("M j, Y" ,strtotime($as_of_date));?></h5>

					        <div class="col-md-12">
					            
					            <hr>
					            
					        </div>
					        <div class="col-md-12">
					            <div class="panel panel-default">
					                <!-- <div class="panel-heading">
					                    <h3 class="text-center"><strong>Order summary</strong></h3>
					                </div> -->
					                <div class="panel-body">
					                    <div class="table-responsive">
					                        <table class="table table-hover">
					                            <thead>
					                                <tr>
					                                    <td class="text-"><strong>Code</strong></td>
					                                    <td><strong>Account</strong></td>
					                                    <td class="text-center"><strong>Debits</strong></td>
					                                    <td class="text-right"><strong>Credits</strong></td>
					                                </tr>
					                            </thead>
					                            <tbody>
					                            	<?php foreach ($trial_balance as $basic_account_id => $t_balance):
					                            		$basic_account = BasicAccountType::find($basic_account_id);
					                            		?>
						                                <tr>
						                                    <td class="text-">
						                                    	<b><?=$basic_account->code;?></b>
						                                    </td>
						                                    <td>
						                                    	<strong>
						                                    		<?=$t_balance['basic_account']['name'];?>
						                                    	</strong>
						                                	</td>
						                                    <td class="text-center"> </td>
						                                    <td class="text-right"> </td>
						                                </tr>
					                            		<?php foreach ($t_balance['accounts'] as $account_id => $account):?>
						                                <tr>
						                                    <td class="text- text-info">
						                                     <?=$account['account_code'];?>
						                                    </td>
						                                    <td class="text-info">
						                                    	<strong class="text-info" style="margin-left: 20px;">
						                                    		<?=$account['account_name'];?>
					                                    		</strong>
						                                	</td>
						                                    <td class="text-center"> 
						                                    	<?php
						                                    	$debits[]= $account['raw_debit_balance'];
						                                    	echo $account['debit_balance'];?></td>
						                                    <td class="text-right">
						                                     <?php

						                                    	$credits[] = $account['raw_credit_balance'];

						                                    	echo $account['credit_balance'];?></td>
						                                </tr>
					                              		<?php endforeach;?>
					                              	<?php endforeach;?>


					                             
					                                <tr>
					                                    <td class="emptyrow text-center">
					                                    </td>
					                                    <td class="emptyrow  text-center"><strong>Total</strong></td>
					                                    <td class="emptyrow text-center">
					                                    	<?=$currency;?><?=ChartOfAccount::account_format(array_sum($debits));?></td>
					                                    <td class="emptyrow text-right">
					                                    	<?=$currency;?><?=ChartOfAccount::account_format(array_sum($credits));?></td>
					                                </tr>
					                            
					                              
					                            </tbody>
					                        </table>
					                    </div>
					                </div>
					            </div>
					        </div>

				<style>
						.height {
						    min-height: 200px;
						}

						.icon {
						    font-size: 47px;
						    color: #5CB85C;
						}

						.iconbig {
						    font-size: 77px;
						    color: #5CB85C;
						}

						.table > tbody > tr > .emptyrow {
						    border-top: none;
						}

						.table > thead > tr > .emptyrow {
						    border-bottom: none;
						}

						.table > tbody > tr > .highrow {
						    border-top: 3px solid;
						}
				</style>






			</div>
		</div>

	</div>
	


<?php include 'inc/footers.php';?>


