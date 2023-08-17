<?php
$page_title = "Factory";
include 'includes/header.php'; ?>
<script src="<?= asset; ?>/angulars/factory.js"></script>


<!-- BEGIN: Content-->
<div class="app-content content" ng-controller="FactoryController" ng-cloak>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <?php include 'includes/breadcrumb.php'; ?>
        <h3 class="content-header-title mb-0">Factory</h3>
      </div>
    </div>
    <div class="content-body">

      <div class="card">
        <div class="card-header">
          <h4 class="card-title" data-toggle="collapse" data-target="#events">Events</h4>
        </div>
        <div class="card-content collapse show" id="events">
          <div class="card-body row">
            <div class="col-md-6">
              <!-- fetcher,  date, category, what to fetch -->
              <form class="row">
                <div class="form-group col-md-6">
                  <label>Date</label>
                  <input type="date" class="form-control" ng-model="$factory.event.form.event_date">
                </div>

                <div class="form-group col-md-6">
                  <label>Fetcher</label>
                  <select class="form-control" ng-model="$factory.event.form.fetcher_id">
                    <option value="">Select Fetcher</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.event.formdata.fetchers">{{$option.name}}</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label>Category</label>
                  <select class="form-control" ng-model="$factory.event.form.category_id">
                    <option value="">Select Category</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.event.formdata.sports_categories">{{$option.name}}</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label>What to fetch</label>
                  <select class="form-control" ng-model="$factory.event.form.what_to_fetch">
                    <option value="">Select what to fetch</option>
                    <option value="{{$option}}" ng-repeat="(index, $option) in $factory.event.formdata.what_to_fetch">{{$option}}</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <button class="btn btn-dark" ng-click="$factory.event.fetch_event();">Fetch Event</button>
                </div>

              </form>

            </div>
            <div class="col-md-6 table-responsive">

              <table class="table-striped table">
                <thead>
                  <th>C</th>
                  <th>E.date</th>
                  <th>Source</th>
                  <th>Data</th>
                </thead>
                <tbody>
                  <tr ng-repeat="(index, $event) in $factory.event.events">
                    <td>{{$event.category.name}}</td>
                    <td>{{$event.event_date}} <span ng-if="isToday($event.event_date)" class="badge badge-success fa fa-check"> </span> </td>
                    <td>{{$event.bookmaker.name}}</td>
                    <td>
                      <span class="badge badge-success" ng-repeat="(index, $label) in $event.n_labels">{{$label}}</span>
                    </td>
                  </tr>
                </tbody>
              </table>


            </div>



          </div>
        </div>
      </div>


      <div class="card">
        <div class="card-header">
          <h4 class="card-title" data-toggle="collapse" data-target="#tipsfactory">Tips</h4>
        </div>
        <div class="card-content collapse show" id="tipsfactory">
          <div class="card-body row">
            <div class="col-md-6">
              <form class="row">
                <div class="form-group col-md-6">
                  <label>Date</label>
                  <input type="date" class="form-control" ng-model="$factory.tips_factory.form.event_date">
                </div>
                <div class="form-group col-md-6">
                  <label>Event Source</label>
                  <select class="form-control" ng-model="$factory.tips_factory.form.event_source_id">
                    <option value="">Select Events Source</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.tips_factory.formdata.fetchers">{{$option.name}}</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label>Bookmaker</label>
                  <select class="form-control" ng-model="$factory.tips_factory.form.bookmaker_id">
                    <option value="">Select Bookmaker</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.tips_factory.formdata.fetchers">{{$option.name}}</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label>Category</label>
                  <select class="form-control" ng-model="$factory.tips_factory.form.category_id">
                    <option value="">Select Category</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.tips_factory.formdata.sports_categories">{{$option.name}}</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label>Paper</label>
                  <select class="form-control" ng-model="$factory.tips_factory.form.paper_id">
                    <option value="">Select Paper</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.tips_factory.formdata.papers">{{$option.name}}</option>
                  </select>
                </div>

                <div class="form-group col-md-6">
                  <label>No of Events</label>
                  <input type="" class="form-control" ng-model="$factory.tips_factory.form.no_of_events">
                </div>

                <div class="form-group col-md-6">
                  <label>Days of Operation</label>
                  <input type="" class="form-control" ng-model="$factory.tips_factory.form.days_of_operations">
                </div>
                <div class="form-group col-md-6">
                  <label>No of Keys</label>
                  <input type="" class="form-control" ng-model="$factory.tips_factory.form.no_of_keys">
                </div>
                <div class="form-group col-md-6">
                  <label>No of Creations</label>
                  <input type="" class="form-control" ng-model="$factory.tips_factory.form.no_of_creations">
                </div>

                <div class="form-group col-md-6">
                  <label>Pricing</label>
                  <input type="" class="form-control" ng-model="$factory.tips_factory.form.pricing">
                </div>


                <div class="form-group col-md-6">
                  <button class="btn btn-dark" ng-click="$factory.tips_factory.create_tips();">Create Tips</button>
                </div>

              </form>

            </div>
            <div class="col-md-6 table-responsive">
              Tips: 32
              <table class="table-striped table">
                <thead>
                  <th>ID# Paper.Author</th>
                  <th>Period</th>
                  <th>Entries/RD</th>
                  <th>Data</th>
                </thead>
                <tbody>
                  <tr>
                    <td>{{$event.category.name}}</td>
                    <td>{{$event.event_date}} <span ng-if="isToday($event.event_date)" class="badge badge-success fa fa-check"> </span> </td>
                    <td>{{$event.bookmaker.name}}</td>
                    <td>
                      <span class="badge badge-success" ng-repeat="(index, $label) in $event.labels">{{$label}}</span>
                    </td>
                  </tr>
                </tbody>
              </table>


            </div>



          </div>
        </div>
      </div>


      <div class="card">
        <div class="card-header">
          <h4 class="card-title" data-toggle="collapse" data-target="#check_performance">Check Tips Performance</h4>
        </div>
        <div class="card-content collapse show" id="check_performance">
          <div class="card-body row">
            <div class="col-md-12">
              <form class="row" action="<?=domain;?>/factory/check_performance" method="POST">
                <div class="form-group col-md-3">
                  <label>Running Date</label>
                  <input type="date" value="<?=date('Y-m-d');?>" class="form-control" name="running_date">
                  <small>The date for which this tips where ran</small>
                </div>

                <div class="form-group col-md-3">
                  <label>Performance Date</label>
                  <input type="date" required=""  value="<?=date('Y-m-d', strtotime("-1 day"));?>"  class="form-control" name="performance_date">
                  <small>The date for which the performance is to be checked</small>
                </div>

                <div class="form-group col-md-3">
                  <label>Per Page</label>
                  <input type="number" step="1" required=""  value=""  class="form-control" name="per_page">
                  <small>Qty to be checked per request</small>
                </div>
               
                <div class="form-group col-md-3">
                  <label>Result Source</label>
                <select class="form-control" ng-model="$factory.tips_factory.form.bookmaker_id" name="result_source_id">
                    <option value="">Select Bookmaker(source)</option>
                    <option value="{{$option.id}}" ng-repeat="(index, $option) in $factory.tips_factory.formdata.fetchers">{{$option.name}}</option>
                  </select>
                </div>

                <div class="form-group col-md-12">
                  <button class="btn btn-dark btn-block">Check Performance</button>
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