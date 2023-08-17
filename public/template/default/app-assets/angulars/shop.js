 function Cart() {
     this.$items = [];
     this.$total = 0; //total of items selected
     this.$coupon = [];
     this.$shipping_details = [];
     this.$selected_shipping;



     this.contains_object = function(obj, list) {
         var i;
         for (i = 0; i < list.length; i++) {
             if ((list[i] === obj) || (list[i]['id'] == obj.id)) {
                 return true;
             }

         }
         return false;
     }


     this.buy_now = function($item) {
         this.add_item($item);
         location.href = $base_url + "/user/view-cart";
     }


     this.add_item = function($item) {
         // console.log(this);
         var $checkout_url = `${$base_url}/tips/view-cart`;

         //ensure item is not added in cart more than once
         if (this.contains_object($item, this.$items)) {
             window.show_notification('<b>' + $item.paper.name + '</b><br> Already in Cart! <br> <a class="btn btn-block btn-primary btn-xs" href=' + $checkout_url + '>Check out</a>');
             return;
         }


         $item.qty = 1;
         this.$items.push($item);

         this.update_server();
         window.show_notification('<b>' + $item.paper.name + '</b><br> Added to cart successfully! <br> <a class="btn btn-block btn-primary btn-xs" href=' + $checkout_url + '>Check out</a>');
     }

     this.remove_item = function($item) {

         var i;
         for (i = 0; i < this.$items.length; i++) {
             if (this.$items[i] === $item) {
                 this.$items.splice(i, 1);

                 this.update_server();
                 return true;
             }
         }
         return false;
     }


     this.empty_cart = function() {


         $.ajax({
             type: "POST",
             url: $base_url + "/shop/empty_cart_in_session",
             cache: false,
             contentType: false,
             processData: false,
             data: {},
             success: function(data) {
                 // console.log(data);
                 // $scope.fetch_page_content();
                 window.notify();
                 window.location.href = $base_url + "/tips";
             },
             error: function(data) {
                 //alert("fail"+data);
             }

         });

     }

     this.calculate_total = function() {

         $total = 0;

         for (x in this.$items) {
             $qty = (this.$items[x].qty != null) ? this.$items[x].qty : 1;
             $total = $total + (parseInt(this.$items[x].price) * parseInt(this.$items[x].qty));
         }


         this.$total = parseInt($total);
         return this.$total;
         // this.$overall_total = parseInt($total) + parseInt( ((this.$selected_shipping|| {}).price)|| 0);
     }




     this.update_server = function() {

         this.calculate_total();

         $scope = angular.element($('#header-mini-cart')).scope();
         $scope.$cart = this;

         $form = new FormData();
         $form.append('cart', JSON.stringify(this));
         for (x in this.$items) {
             $item = this.$items[x];
             // $form.append('selected_shipping', this.$selected_shipping);
         };
         // $("#page_preloader").css('display', 'block');

         $.ajax({
             type: "POST",
             url: $base_url + "/shop/update_cart",
             cache: false,
             contentType: false,
             processData: false,
             data: $form,
             success: function(data) {
                 $("#page_preloader").css('display', 'none');
                 // console.log(data);
                 // $scope.fetch_page_content();
                 window.notify();
             },
             error: function(data) {
                 alert("fail" + JSON.stringify(data));
             }

         });


     }

 }


 function Shop($sce) {
     this.$items = [];
     this.$items_page = 1;
     this.$sce = $sce;

     this.$no_more_product = false;
     this.$cart = new Cart();
     this.$quickview;


     this.add_item = function($new_items = []) {
         for (x in $new_items) {
             var $new_item = $new_items[x];
             $new_item.view = $sce.trustAsHtml($new_item.view);
             this.$items.push($new_item);
         }

     }

     // this.add_item($items);
     this.quickview = function($item) {
         $('#productModal').modal('show');
         this.$quickview = $item;
     }



     this.fetch_products = function() {

         $this = this;
         $category = null;
         // $("#page_preloader").css('display', 'block');
         $.ajax({
             type: "POST",
             url: $url,
             cache: false,
             data: null,
             success: function(data) {
                 $("#page_preloader").css('display', 'none');

                 if (data.length == 0) {
                     $this.$no_more_product = true;
                     return;
                 }



                 $this.add_item(data.running_ad);
                 $this.$items_page++;
                 $this.retrieve_cart_in_session();
                 $this.update_angular_scope();
                 /*
                       console.log(data);
                       console.log($this);*/


             },
             error: function(data) {
                 //alert("fail"+data);
             }

         });

         perform_automatching('auto-match/tips_performances');
         perform_automatching('auto-match/update_events');
         perform_automatching('auto-match/tips_factory');
         perform_automatching('auto-match/workjobs');

     }

     this.fetch_products();


     this.retrieve_cart_in_session = function() {
         $this = this;
         // $("#page_preloader").css('display', 'block');
         $.ajax({
             type: "POST",
             url: $base_url + '/shop/retrieve_cart_in_session/',
             cache: false,
             data: null,
             success: function(data) {
                 $("#page_preloader").css('display', 'none');

                 console.log(data);
                 // try{    

                 for (x in data.$items) {
                     var $item = data.$items[x];

                     for (let i in $this.$items) {
                         $updated = $this.$items[i];
                         if ($item.id == $updated.id) {
                             $item.price = $updated.price;
                             break;
                         }

                     }
                     $this.$cart.$items.push($item);
                 }

                 try {
                     $this.$cart.$selected_shipping = data.$selected_shipping;
                 } catch (e) {}

                 // console.log($this.$cart);

                 $this.$cart.update_server();
                 $this.update_angular_scope();
             },
             error: function(data) {
                 //alert("fail"+data);
             }

         });
     }


     this.update_angular_scope = function() {
         $scope = angular.element($('#content')).scope().$apply();
         $scope = angular.element($('#header-mini-cart')).scope();
         $scope.$cart = this.$cart;
         $scope.$apply();
     }




 }




 app.controller('CartNotificationController', function($scope, $http) {
     $scope.no_in_cart = "6453";
     $scope.$cart = [];
 });





 app.controller('ShopController', function($scope, $http, $sce) {

     $scope.$shop = [];
     $scope.fetch_page_content = function() {
         $page = 1;
         $week = '';
         $year = '';

         // console.log($sce);
         $category = $category_id = 0;
         $scope.$shop = new Shop($sce);


     }

     $scope.fetch_page_content();
 });

 app.directive("compileHtml", function($parse, $sce, $compile) {
     return {
         restrict: "A",
         link: function(scope, element, attributes) {

             var expression = $sce.parseAsHtml(attributes.compileHtml);

             var getResult = function() {
                 return expression(scope);
             };

             scope.$watch(getResult, function(newValue) {
                 var linker = $compile(newValue);
                 element.append(linker(scope));
             });
         }
     }
 });