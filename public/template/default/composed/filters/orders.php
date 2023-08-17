           <div class="dropdown dropleft" style="display: inline;">
               <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                   <i class="fa fa-filter"></i>
               </button>

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


                           <div class="form-group col-md-6 col-xs-12">
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

                           <div class="form-group col-md-6 col-xs-12">
                               <label>Currency</label>
                               <select class="form-control" name="currency">
                                   <option value="">Select</option>
                                   <?php
                                    $currency_value = Config::currency('code');
                                    foreach (AvailableCurrency::available()->orderBy('code')->get() as $key => $global_currency) : ?>
                                       <option <?= ($global_currency->code == $currency_value) ? 'selected' : ''; ?> value="<?= $global_currency->code; ?>"><?= $global_currency->code; ?> (<?= $global_currency->html_code; ?>)</option>
                                   <?php endforeach; ?>

                               </select>

                           </div>

                       </div>

                       <!-- 
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
 -->

                       <div class="row">

                           <div class=" form-group col-md-6 col-xs-12">
                               <label>* Paid (From):</label>
                               <input placeholder="Start" type="date" value="<?= $sieve['paid_at']['start_date'] ?? date("Y-m-d"); ?>" class="form-control" name="paid_at[start_date]">
                           </div>


                           <div class=" form-group col-md-6 col-xs-12">
                               <label>* Paid (To)</label>
                               <input type="date" placeholder="End " value="<?= $sieve['paid_at']['end_date'] ?? date("Y-m-d"); ?>" class="form-control" name="paid_at[end_date]">
                           </div>


                       </div>


                       <div class="form-group">
                           <button type="Submit" class="btn btn-primary">Submit</button>
                           <!-- <a  onclick="$('#filter_form').reset()">Reset</a> -->
                       </div>
                   </form>

               </div>
           </div>