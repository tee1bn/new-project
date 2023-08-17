                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-filter"></i>
                            </button>
                            <div class="dropdown-menu" style="padding: 20px;">
                                <form action="<?= $action ?? ''; ?>" method="get" id="filter_form">
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <label>User</label>
                                            <input type="" name="user" placeholder="name/username/email" class="form-control" value="<?= $sieve['user'] ?? ''; ?>">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label>Bill ID</label>
                                            <input type="" name="bill_id" class="form-control" value="<?= $sieve['bill_id'] ?? ''; ?>">
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label>Bill Type</label><br>
                                            <select class="form-control" name="bill_type">
                                                <option value="">Select</option>
                                                <?php foreach (['token', 'unit', 'plan'] as $key => $value) : ?>
                                                    <option <?= ((isset($sieve['bill_type'])) && ($sieve['bill_type'] == $value) || (isset($sieve['bill_type']) && $subscription['type'] == $value)) ? 'selected' : ''; ?> value="<?= $value; ?>">
                                                        <?= $value; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class=" form-group col-sm-6">
                                        </div>

                                        <div class=" form-group col-sm-6">
                                            <label>* date(From):</label>
                                            <input placeholder="Start" type="date" value="<?= $sieve['usage_date']['start_date'] ?? ''; ?>" class="form-control" name="usage_date[start_date]">
                                        </div>


                                        <div class=" form-group col-sm-6">
                                            <label>* date (To)</label>
                                            <input type="date" placeholder="End " value="<?= $sieve['usage_date']['end_date'] ?? ''; ?>" class="form-control" name="usage_date[end_date]">
                                        </div>



                                    </div>
                                    <div class="form-group">
                                        <button type="Submit" class="btn btn-primary">Submit</button>
                                        <!-- <a  onclick="$('#filter_form').reset()">Reset</a> -->
                                    </div>
                                </form>

                            </div>
                        </div>