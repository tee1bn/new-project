           <div class="dropdown " style="display: inline;">
               <a href="javascript:void(0);" class=" dropdown-toggle" data-toggle="dropdown">
                   Filter
                   <!-- <i class="fa fa-filter"></i> -->
               </a>

               <div class="dropdown-menu" style="padding: 20px; ">
                   <form action="<?= $action ?? ''; ?>" method="get" id="filter_form" style="width:400px;">
                       <div class="row">
                           <div class="form-group col-md-6 col-xs-12">
                               <label>Ref</label><br>
                               <input type="" name="ref" class="form-control" value="<?= $sieve['ref'] ?? ''; ?>">
                           </div>


                           <div class="form-group col-md-6 col-xs-12">
                               <label>Paid Status</label>
                               <select class="form-control" name="payment_status">
                                   <option value="">Select</option>
                                   <?php foreach (['unpaid' => 'UnPaid', 'paid' => 'Paid',] as $key => $value) : ?>
                                       <option <?= (isset($sieve['payment_status']) &&  $sieve['payment_status'] == $key) ? 'selected' : ''; ?> value="<?= $key; ?>">
                                           <?= $value; ?>
                                       </option>
                                   <?php endforeach; ?>
                               </select>
                           </div>

                       </div>


                       <div class="row">


                           <!-- <div class="form-group col-md-6 col-xs-12">
                               <label>Payment Method</label>
                               <select class="form-control" name="payment_method">
                                   <option value="">Select</option>
                                   <?php foreach ($shop->available_payment_method as $key => $value) : ?>
                                       <option <?= (isset($sieve['payment_method']) && $sieve['payment_method'] == $value['name']) ? 'selected' : ''; ?> value="<?= $key; ?>">
                                           <?= $value['name']; ?>
                                       </option>
                                   <?php endforeach; ?>
                               </select>
                           </div> 
                        
                        -->



                       </div>

                       <div class="row">

                           <div class=" form-group col-md-6 col-xs-12">
                               <label>* Ordered(From):</label>
                               <input placeholder="Start" type="date" value="<?= $sieve['ordered']['start_date'] ?? ''; ?>" class="form-control" name="ordered[start_date]">
                           </div>


                           <div class=" form-group col-md-6 col-xs-12">
                               <label>* Ordered (To)</label>
                               <input type="date" placeholder="End " value="<?= $sieve['ordered']['end_date'] ?? ''; ?>" class="form-control" name="ordered[end_date]">
                           </div>

                       </div>

                       <!-- <div class="row">

                           <div class=" form-group col-md-6 col-xs-12">
                               <label>* Paid (From):</label>
                               <input placeholder="Start" type="date" value="<?= $sieve['paid_at']['start_date'] ?? ''; ?>" class="form-control" name="paid_at[start_date]">
                           </div>


                           <div class=" form-group col-md-6 col-xs-12">
                               <label>* Paid (To)</label>
                               <input type="date" placeholder="End " value="<?= $sieve['paid_at']['end_date'] ?? ''; ?>" class="form-control" name="paid_at[end_date]">
                           </div>


                       </div> -->


                       <div class="form-group">
                           <button type="Submit" class="btn btn-primary">Submit</button>
                           <!-- <a  onclick="$('#filter_form').reset()">Reset</a> -->
                       </div>
                   </form>

               </div>
           </div>