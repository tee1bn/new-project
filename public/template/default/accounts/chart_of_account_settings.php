<?php include 'inc/headers.php';?>

<div class="container" >

		<div class="row">
			<div class="col-md-12">
				<h3><?=$chart_of_account->account_name;?></h3>
				<small><?=$chart_of_account->OpenOrCloseStatus;?></small>
			</div>


						


			<hr>




			<div class="panel-group col-md-12" style="margin-top: 10px;">
			  <div class="panel panel-default">
			    <div id="collapse1" class="panel-collapse collapse in">
			      <div class="panel-body">
					<div class="col-md-6">
						<h3>Opening Balance</h3>
						<span><?=$currency;?><?=$this->money_format(intval($chart_of_account->opening_balance));?> </span>
					</div>

					<div class="col-md-6">
						<h3>Current Balance</h3>
						<span><?=$currency;?><?=$this->money_format(intval($chart_of_account->current_balance));?> </span>
					</div>


			      </div>
			    </div>
			  </div>
			</div>
				
			<div class="panel-group col-md-12" style="margin-top: 10px;">
			  <div class="panel panel-default">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" href="#collapse1">Settings</a>
			      </h4>
			    </div>
			    <div id="collapse1" class="panel-collapse collapse in">
			      <div class="panel-body">
					<div class="col-md-12">
							<form id="create_chart_of_account_category_form" action="<?=domain;?>/accounts/update_chart_of_account" method="post" >  
				      		<div class="form-group">
				      			<label>Account Type *</label>
				      			<select name="account_type" class="form-control">
										<?php foreach ($options as $base_category => $sub_categories):?>
											<optgroup   label="<?=$base_category;?>">
											<?php foreach ($sub_categories as $sub_category):?>

													<option value="<?=$sub_category['id'];?>" >
														<?=$sub_category['name'];?>
													</option>

											<?php endforeach ;?>
											</optgroup>
										<?php endforeach ;?>
									</select>

				      		
				      		</div>
				      		<input type="hidden" name="id" value="<?=$chart_of_account->id;?>">
				      		<div class="form-group">
				      			<label>Account Name*</label>
				      			<input type="" name="name" value="<?=$chart_of_account->account_name;?>" required="" class="form-control"> 
				      		</div>
						
							<!-- <div class="form-group">
				      			<label>Opening Balance*</label>
				      			<input type="" name="name" value="<?=$chart_of_account->account_name;?>" required="" class="form-control"> 
				      		</div> -->

						      <div class="modal-footer">
						        <button type="submit" class="btn btn-danger" >Save</button>
						      </div>
				      	</form>
					</div>			      	
			      	
			      </div>
			      <!-- <div class="panel-footer">Panel Footer</div> -->
			    </div>
			  </div>
			</div>
						


		</div>





	
	</div>
<?php include 'inc/footers.php';?>


