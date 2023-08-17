<?php
$page_title = "Settings";
include 'includes/header.php'; ?>

<script type="text/javascript" src="<?= $this_folder; ?>/angularjs/settings.js"></script>
<script src="<?= asset; ?>/angulars/admin_settings.js"></script>



<!-- BEGIN: Content-->
<div ng-controller="Settings" ng-cloak class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <?php include 'includes/breadcrumb.php'; ?>

        <h3 class="content-header-title mb-0">Settings</h3>
      </div>

      <!--  <div class="content-header-right col-md-6 col-12">
            <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
              <div class="btn-group" role="group">
                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> Settings</button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1"><a class="dropdown-item" href="card-bootstrap.html">Bootstrap Cards</a><a class="dropdown-item" href="component-buttons-extended.html">Buttons Extended</a></div>
              </div><a class="btn btn-outline-primary" href="full-calender-basic.html"><i class="ft-mail"></i></a><a class="btn btn-outline-primary" href="timeline-center.html"><i class="ft-pie-chart"></i></a>
            </div>
          </div> -->
    </div>
    <div class="content-body">

      <div class="row">
        <div class="col-12">
          <div class="card">

            <div style="" class="card-header" data-toggle="collapse" data-target="#payment_gateway_settings">
              <a href="javascript:void;" class="card-title">Payment Gateways Settings</a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
              </div>
            </div>
            <div class="card-body row collapse " id="payment_gateway_settings">

              <div class="col-12" ng-repeat=" ($index , $gateway) in $payment_gateway_settings">
                <div class="card card-bordered">

                  <div class="card-header" data-toggle="collapse" data-target="#payment_gateway_settings{{$index}}">
                    <a href="javascript:void;" class="card-title">{{$gateway.name}}</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="card-body row collapse " id="payment_gateway_settings{{$index}}">



                    <div class="col-6" ng-repeat=" (key , $setting) in $gateway.json_settings">
                      <div class="card">

                        <div class="card-header" data-toggle="collapse" data-target="#gateway_settings{{$index}}">
                          <a href="javascript:void;" class="card-title">{{key}}</a>
                        </div>
                        <div class="card-body row collapse show " id="gateway_settings{{$index}}">


                          <div class="form-group col-md-12" ng-repeat="(key, $input) in $setting" ng-init="kkey = key">
                            <label> {{kkey}} </label>
                            <input type="" ng-model="$setting[key]" class="form-control" name="">
                          </div>


                        </div>

                      </div>
                    </div>

                    <form class="col-md-12 ajax_form" method="post" action="<?= domain; ?>/settings/update_payment_settings">

                      <input type="" style="display:none;" name="criteria" value="{{$gateway.criteria}}">
                      <textarea style="display: none;" class="form-control" name="settings">{{$gateway}}</textarea>

                      <button class="form-control btn-success">Update</button>

                    </form>

                  </div>


                </div>
              </div>





            </div>

          </div>
        </div>
      </div>






      <div class="row">
        <div class="col-12">
          <div class="card">

            <div class="card-header" data-toggle="collapse" data-target="#demo">
              <a href="javascript:void;" class="card-title">Settings</a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
              </div>

            </div>
            <div class="card-body row collapse" id="demo">

              <div ng-repeat="($key, $setting) in $site_settings" class="form-group col-md-6">
                <span class="badge badge-secondary">{{$index+1}}</span>
                <label>{{$key |replace: '_':' '}}</label>
                <input type="" placeholder="{{$key}}" ng-model="$site_settings[$key]" class="form-control">
              </div>



              <form action="<?= domain; ?>/settings/update_site_settings" method="post" class="ajax_form" id="site_settings_form">

                <textarea style="display: none;" name="content">{{$site_settings}}</textarea>


                <div class="text-center col-12">
                  <button ng-show="$site_settings.length != 0" class="btn btn-success" type="submit">Update
                  </button>
                </div>
              </form>

            </div>

          </div>
        </div>
      </div>



      <div class="row" style="display:none;">
        <div class="col-12">
          <div class="card">

            <div class="card-header" data-toggle="collapse" data-target="#BonusSettings">
              <a href="javascript:void;" class="card-title">Bonus Settings</a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
              </div>

            </div>
            <div class="card-body row collapse" id="BonusSettings">
              <div class="col-12">


                <div class="card">

                  <div class="card-header" data-toggle="collapse" data-target="#DirectBonus">
                    <a href="javascript:void;" class="card-title">Direct Bonus</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>

                  </div>
                  <div class="card-body row collapse" id="DirectBonus">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Generation</th>
                          <th>X Days </th>
                          <th>First x days percent (%)</th>
                          <th>After x days percent (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="(key, $setting) in $direct_bonus.direct_bonus">
                          <td>{{$index + 1}}</td>
                          <td>{{$setting.level}}</td>
                          <td contenteditable="true" ng-model="$setting.x_days">{{$setting.x_days}}</td>
                          <td contenteditable="true" ng-model="$setting.first_x_days_percent">{{$setting.first_x_days_percent}}</td>
                          <td contenteditable="true" ng-model="$setting.after_x_days_percent">{{$setting.after_x_days_percent}}</td>
                        </tr>

                      </tbody>
                    </table>

                    <div class="form-group col-12">
                      <label>Direct Bonus is paid (interval)</label>
                      <input type="" name="" ng-model="$direct_bonus.is_paid">
                    </div>


                    <form action="<?= domain; ?>/settings/update/direct_bonus" method="post" class="ajax_form" id="direct_bonus_form">

                      <textarea style="display: none;" name="content">{{$direct_bonus}}</textarea>

                      <div class="text-center col-12">
                        <button ng-show="$direct_bonus.length != 0" class="btn btn-success" type="submit">Update </button>
                      </div>
                    </form>

                  </div>

                </div>

                <div class="card">

                  <div class="card-header" data-toggle="collapse" data-target="#BinaryBonus">
                    <a href="javascript:void;" class="card-title">Binary Bonus</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>

                  </div>
                  <div class="card-body row collapse" id="BinaryBonus">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Generation</th>
                          <th>Pack 1 (%) </th>
                          <th>Pack 2 (%) </th>
                          <th>Pack 3 (%) </th>
                          <th>Pack 4 (%) </th>
                          <th>Pack 5 (%) </th>
                          <th>Pack 6 (%) </th>
                          <th>Pack 7 (%) </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="(key, $setting) in $binary_bonus.binary_bonus">
                          <td>{{$index + 1}}</td>
                          <td>{{$setting.level}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_1">{{$setting.pack_1}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_2">{{$setting.pack_2}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_3">{{$setting.pack_3}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_4">{{$setting.pack_4}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_5">{{$setting.pack_5}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_6">{{$setting.pack_6}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_7">{{$setting.pack_7}}</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="form-group col-12">
                      <label>Expiry (days)</label>
                      <input type="" name="" ng-model="$binary_bonus.expiry_in_days">
                    </div>

                    <div class="form-group col-12">
                      <label>Binary Bonus cuts on (weekday)</label>
                      <input type="" name="" ng-model="$binary_bonus.cuts_on">
                    </div>

                    <div class="form-group col-12">
                      <label>Binary Bonus is Paid Every (weekday)</label>
                      <input type="" name="" ng-model="$binary_bonus.is_paid_every">
                    </div>

                    <form action="<?= domain; ?>/settings/update/binary_bonus" method="post" class="ajax_form" id="binary_bonus_form">

                      <textarea style="display: none;" name="content">{{$binary_bonus}}</textarea>

                      <div class="text-center col-12 ">
                        <button ng-show="$binary_bonus.length != 0" class="btn btn-success" type="submit">Update </button>
                      </div>
                    </form>

                  </div>

                </div>

                <div class="card">

                  <div class="card-header" data-toggle="collapse" data-target="#MatchingBonus">
                    <a href="javascript:void;" class="card-title">Matching Bonus</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>

                  </div>
                  <div class="card-body row collapse" id="MatchingBonus">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Generation</th>
                          <th>Pack 3 (%)</th>
                          <th>Pack 4 (%)</th>
                          <th>Pack 5 (%)</th>
                          <th>Pack 6 (%)</th>
                          <th>Pack 7 (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="(key, $setting) in $matching_bonus">
                          <td>{{$index + 1}}</td>
                          <td>{{$setting.level}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_3">{{$setting.pack_3}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_4">{{$setting.pack_4}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_5">{{$setting.pack_5}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_6">{{$setting.pack_6}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_7">{{$setting.pack_7}}</td>
                        </tr>

                      </tbody>
                    </table>

                    <form action="<?= domain; ?>/settings/update/matching_bonus" method="post" class="ajax_form" id="matching_bonus_form">

                      <textarea style="display: none;" name="content">{{$matching_bonus}}</textarea>

                      <div class="text-center col-12">
                        <button ng-show="$matching_bonus.length != 0" class="btn btn-success" type="submit">Update </button>
                      </div>
                    </form>

                  </div>

                </div>


                <div class="card">

                  <div class="card-header" data-toggle="collapse" data-target="#SpeakerBonus">
                    <a href="javascript:void;" class="card-title">Speaker Bonus</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>

                  </div>
                  <div class="card-body row collapse" id="SpeakerBonus">
                    <!--  <table class="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Generation</th>
                          <th>Pack 3 (%)</th>
                          <th>Pack 4 (%)</th>
                          <th>Pack 5 (%)</th>
                          <th>Pack 6 (%)</th>
                          <th>Pack 7 (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="(key, $setting) in $speaker_bonus.speaker_bonus">
                          <td>{{$index + 1}}</td>
                          <td>{{$setting.level}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_3">{{$setting.pack_3}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_4">{{$setting.pack_4}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_5">{{$setting.pack_5}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_6">{{$setting.pack_6}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_7">{{$setting.pack_7}}</td>
                        </tr>

                      </tbody>
                    </table> -->


                    <div class="form-group col-12">
                      <label>Speaker receives (%)</label>
                      <input type="" class="form-control" name="" ng-model="$speaker_bonus.speaker_receives_percent">
                    </div>


                    <div class="form-group col-12">
                      <label>Throughout x days after presentation (days)</label>
                      <input type="" class="form-control" name="" ng-model="$speaker_bonus.until_x_days_after_presentation">
                    </div>


                    <div class="form-group col-12">
                      <label>Turnover of conference (%)</label>
                      <input type="" class="form-control" name="" ng-model="$speaker_bonus.turnover_of_conference_percent">
                    </div>


                    <form action="<?= domain; ?>/settings/update/speaker_bonus" method="post" class="ajax_form" id="speaker_bonus_form">

                      <textarea style="display: none;" name="content">{{$speaker_bonus}}</textarea>

                      <div class="text-center col-12">
                        <button ng-show="$speaker_bonus.length != 0" class="btn btn-success" type="submit">Update </button>
                      </div>
                    </form>

                  </div>

                </div>

                <div class="card">

                  <div class="card-header" data-toggle="collapse" data-target="#OfficeBonus">
                    <a href="javascript:void;" class="card-title">Office Bonus</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>

                  </div>
                  <div class="card-body row collapse" id="OfficeBonus">
                    <!--  <table class="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Generation</th>
                          <th>Pack 3 (%)</th>
                          <th>Pack 4 (%)</th>
                          <th>Pack 5 (%)</th>
                          <th>Pack 6 (%)</th>
                          <th>Pack 7 (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="(key, $setting) in $office_bonus.office_bonus">
                          <td>{{$index + 1}}</td>
                          <td>{{$setting.level}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_3">{{$setting.pack_3}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_4">{{$setting.pack_4}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_5">{{$setting.pack_5}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_6">{{$setting.pack_6}}</td>
                          <td contenteditable="true" ng-model="$setting.pack_7">{{$setting.pack_7}}</td>
                        </tr>

                      </tbody>
                    </table> -->


                    <div class="form-group col-12">
                      <label>Office Opener receives (%)</label>
                      <input type="" class="form-control" name="" ng-model="$office_bonus.office_opener_receives_percent">
                    </div>




                    <div class="form-group col-12">
                      <label>Turnover of country or city (%)</label>
                      <input type="" class="form-control" name="" ng-model="$office_bonus.turnover_of_country_or_city_percent">
                    </div>


                    <form action="<?= domain; ?>/settings/update/office_bonus" method="post" class="ajax_form" id="">

                      <textarea style="display: none;" name="content">{{$office_bonus}}</textarea>

                      <div class="text-center col-12">
                        <button ng-show="$office_bonus.length != 0" class="btn btn-success" type="submit">Update </button>
                      </div>
                    </form>

                  </div>

                </div>



                <div class="card">

                  <div class="card-header" data-toggle="collapse" data-target="#AutoBonus">
                    <a href="javascript:void;" class="card-title">Auto Bonus</a>
                    <div class="heading-elements">
                      <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                      </ul>
                    </div>

                  </div>
                  <div class="card-body row collapse" id="AutoBonus">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>SN</th>
                          <th>Downlines (Generation)</th>
                          <th>Billing (%)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr ng-repeat="(key, $setting) in $auto_bonus.auto_bonus">
                          <td>{{$index + 1}}</td>
                          <td>{{$setting.level}}</td>
                          <td contenteditable="true" ng-model="$setting.billing_percent">{{$setting.billing_percent}}</td>
                        </tr>

                      </tbody>
                    </table>

                    <form action="<?= domain; ?>/settings/update/auto_bonus" method="post" class="ajax_form" id="">

                      <textarea style="display: none;" name="content">{{$auto_bonus}}</textarea>

                      <div class="text-center col-12">
                        <button ng-show="$auto_bonus.length != 0" class="btn btn-success" type="submit">Update </button>
                      </div>
                    </form>

                  </div>

                </div>



              </div>
            </div>

          </div>
        </div>
      </div>



      <div class="row" style="display:none;">
        <div class="col-12">
          <div class="card">

            <div class="card-header" data-toggle="collapse" data-target="#LeadershipProgram">
              <a href="javascript:void;" class="card-title">Leadership Program Settings</a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
              </div>

            </div>

            <style>
              td>span {
                /*border: 1px solid #00000033;*/
                margin: 1px;
                padding: 5px;
              }
            </style>


            <div class="card-body row collapse show" id="LeadershipProgram">
              <div class="col-12 table-responsive">
                <!-- {{$leadership_ranks.rank_qualifications}} -->

                <table class="table">
                  <thead>
                    <tr>
                      <th>SN</th>
                      <th>Rank</th>
                      <th>Points (Volume) </th>
                      <th>Ratings (Strong Line)</th>
                      <th>Cash Rewards</th>
                      <th>Binary Gain</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="(key, $setting) in $leadership_ranks.rank_qualifications">
                      <td>{{$index + 1}}</td>
                      <td>
                        <span contenteditable="true" ng-model="$leadership_ranks.all_ranks[key].name"></span>
                      </td>
                      <td>
                        <span contenteditable="true" ng-model="$setting.points_volume.activity.action"></span>
                        <span contenteditable="true" ng-model="$setting.points_volume.points"></span>
                      </td>

                      <td>
                        <!-- {{$setting.rating.in_team}} -->
                        <span contenteditable="true" ng-model="$setting.rating.activity.action"></span><br>


                        <span ng-repeat="($index, $in_team) in $setting.rating.in_team">
                          <span contenteditable="true" ng-model="$in_team.count"></span>


                          <select ng-model="$setting.rating.in_team[$index].member_rank">
                            <option value="">Please Select</option>
                            <option value="{{$rank.index}}" ng-repeat="($index, $rank) in $leadership_ranks.all_ranks" ng-selected="$rank.index==$in_team.member_rank">{{$rank.name}}</option>
                          </select>
                          <br>
                        </span>



                        <span ng-repeat="($index, $direct_line) in $setting.rating.direct_line">
                          <span contenteditable="true" ng-model="$direct_line.count"></span>
                          <span contenteditable="true" ng-model="$direct_line.position"></span>
                          <br>
                        </span>

                      </td>
                      <td>
                        <span contenteditable="true" ng-model="$setting.cash_rewards.amount"></span>
                        <br>

                        <span contenteditable="true" ng-model="$setting.cash_rewards.perks"></span>

                      </td>
                      <td>
                        <!-- {{$setting.binary_gains}} -->
                        <!-- the limit on binary bonus per day for each user -->
                        <span contenteditable="true" ng-model="$setting.binary_gains"></span><br>


                      </td>


                    </tr>

                  </tbody>
                </table>

                <form action="<?= domain; ?>/settings/update/leadership_ranks" method="post" class="ajax_form" id="leadership_ranks_form">

                  <textarea style="display: none;" name="content">{{$leadership_ranks}}</textarea>

                  <div class="text-center col-12">
                    <button ng-show="$leadership_ranks.length != 0" class="btn btn-success" type="submit">Update </button>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>



      <div class="row">
        <div class="col-12">
          <div class="card">

            <div class="card-header" data-toggle="collapse" data-target="#Rules">
              <a href="javascript:void;" class="card-title">Rules Settings</a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
              </div>

            </div>

            <div class="card-body row collapse" id="Rules">
              <div class="col-12">
                <div class="row">

                  <div class="form-group col-md-6">
                    <label>Withdrawal Fee (%)</label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.withdrawal_fee_percent">
                  </div>


                  <div class="form-group col-md-6">
                    <label>Minimum Withdrawal </label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.min_withdrawal_usd">
                  </div>

                  <div class="form-group col-md-6">
                    <label>Referral Percentage (%) </label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.referral_percent">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Minimum Deposit </label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.min_deposit_usd">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Low Limit Minimum Days</label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.low_limit_minimum_days">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Low Limit Minimum Unit</label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.low_limit_minimum_unit">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Minimum Notice Intervals</label>
                    <input type="" class="form-control" name="" ng-model="$rules_settings.minimum_notice_interval">
                  </div>


                </div>

              </div>


              <form action="<?= domain; ?>/settings/update/rules_settings" method="post" class="ajax_form" id="rules_settings_form">

                <textarea style="display: none;" name="content">{{$rules_settings}}</textarea>

                <div class="text-center col-12">
                  <button ng-show="$rules_settings.length != 0" class="btn btn-success" type="submit">Update </button>
                </div>
              </form>





            </div>
          </div>

        </div>

        


        <?php //$live_chat_installation = SiteSettings::where('criteria', 'live_chat_installation')->first();
        ?>

        <div class="card col-md-12">

          <div class="card-header collapsed" aria-expanded="false" data-toggle="collapse" data-target="#demo_live_chat">
            <a href="javascript:void(0);">Edit Live Chat installation code </a>
          </div>
          <div class="card-body collapse show" id="demo_live_chat">
            <form class="ajax_form" action="<?= domain; ?>/settings/update/live_chat_installation" method="post">
              <div class="card-body">



                <div class="form-group">

                  <textarea rows="8" required="" name="content" class="form-control"><?= '' //$live_chat_installation->settings;
                                                                                      ?></textarea>
                </div>

                <button class="btn  btn-success pull-right">Save</button>


              </div>




            </form>
          </div>
        </div>

      </div>



    </div>



  </div>
</div>
</div>
<!-- END: Content-->

<?php include 'includes/footer.php'; ?>