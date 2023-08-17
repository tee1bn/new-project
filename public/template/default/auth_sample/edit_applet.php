<?php
$page_title = "Edit Applet";
include_once 'includes/header.php'; ?>

<script src="https://unpkg.com/vue@3"></script>

<!-- <script src="http://localhost/convertbetcodes/applet/applet.js?id=ckFDeUx5WVFtN2g3dzcyWTFjcFhMOUZ3TFJJbFFmNUpJN0phVWFVa0JlND0="></script> -->
<!-- <script src="<?= $domain; ?>/applet/applet.js?id=ckFDeUx5WVFtN2g3dzcyWTFjcFhMOUZ3TFJJbFFmNUpJN0phVWFVa0JlND0=" data-s=""></script> -->

<div id="app">
    <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>

            <?php include_once 'includes/breadcrumb.php'; ?>

            <h4 class="mg-b-0 tx-spacing--1">Edit Applet</h4>
        </div>
        <div class=" d-md-block">
            <!-- <button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button> -->
            <a href="<?= domain; ?>/user/applets" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"> Applets</a>
        </div>
    </div>


    <div class="row">

        <!--         <div class="col-md-12 mg-t-10">
            <div class="card ht-100p">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mg-b-0">Applets </h6>
                    <div class="d-flex tx-18">
                        <a href="<?= domain; ?>/user/create_applet" class="link-03 lh-0">+ New Applet<i class="icon ion-md-refresh"></i></a>
                        <a href="#" class="link-03 lh-0 mg-l-10"><i class="icon ion-md-more"></i></a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <div class="col-md-6">
            <div class="card">
                <!-- <div class="card-header">Featured</div> -->
                <div class="card-body">
                    <form class="mb-10" @submit.prevent="saveChanges">
                        <!-- <legend>Personal Information</legend> -->
                        <div class="form-group">
                            <label for="">Name</label>
                            <input v-model="applet.name" type="text" placeholder="E.g MyApp" class="form-control" required>
                            <small class="form-text text-muted">Everything has a name, name your applet.</small>
                        </div>


                        <div class="form-group">
                            <label for="">Domain</label>
                            <input v-model="applet.details.domain" type="text" placeholder="domain.com" class="form-control" required>
                            <small class="form-text text-muted">Where will you install this? Use <code>*</code> to allow all domain.</small>
                        </div>

                        <div class="row">

                            <div class="form-group col-6">
                                <label>Enable
                                    <input v-model="applet.status" class="checkbox" type="checkbox" false-value="0" true-value="1">
                                    <small class="form-text text-muted">Check this to turn on your applet.</small>
                                </label>
                            </div>

                            <div cplass="form-group col-6">
                                <label>button trigger
                                    <input v-model="applet.details.button.style.display" false-value="none" true-value="block" class="checkbox" type="checkbox">
                                    <small v-if="applet.details.button.style.display=='block'" class="form-text text-muted">A button on the screen.</small>
                                    <small v-if="applet.details.button.style.display!='block'" class="form-text text-muted">
                                        Add: <code onclic="copy_text()">onclick="loadConvertBetCodes()"</code> </small>

                                </label>
                            </div>
                        </div>



                        <div class="form-group">
                            <label>Button color</label>
                            <input v-model="applet.details.button.style.backgroundColor" class="form-control" type="color">
                            <small class="form-text text-muted">Match your brand</small>
                        </div>


                        <p class="mg-b-10">Select bookies at home <span class="tx-danger">*</span>
                            <br><span class=" float-righ">
                                <label>All <input @change="select_all_bookies($event, 'home_bookies')" class="checkbox" type="checkbox"></label>
                                |<label>Invert <input @change="invert_selection($event, 'home_bookies')" class="checkbox" type="checkbox"></label>
                            </span>

                        </p>
                        <div class="form-group row">
                            <div class="col-md-3 col-4 col-lg-3" v-for="(item, index) in data.home_bookies" :key="index" style="padding-left:10px;">
                                <label class="badge badge-sm badge" style="margin:0;padding:0;">{{item.bookie}}
                                    <input @change="select_bookie(item, 'home_bookies')" class="checkbox" type="checkbox" :checked="is_bookie_selected(item, 'home_bookies')">
                                </label>
                            </div>
                        </div>





                        <!-- <label>Destination bookies</label> -->
                        <p class="mg-b-10">Select bookies at destination <span class="tx-danger">*</span>
                            <br><span class=" float-righ">
                                <label>All <input @change="select_all_bookies($event)" class="checkbox" type="checkbox"></label>
                                |<label>Invert <input @change="invert_selection($event)" class="checkbox" type="checkbox"></label>
                            </span>

                        </p>
                        <div class="form-group row">
                            <div class="col-md-3 col-4 col-lg-3" v-for="(item, index) in data.destination_bookies" :key="index" style="padding-left:10px;">
                                <label class="badge badge-sm badge" style="margin:0;padding:0;">{{item.bookie}}
                                    <input @change="select_bookie(item)" class="checkbox" type="checkbox" :checked="is_bookie_selected(item)">
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 50px;margin-bottom: 0px;">
                            <button class="btn btn-outline-primary btn-block"> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pop-up Installation</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Instruction</h6>
                    <p class="card-text">Please copy the code below and place in the <code>&lt;head&gt;</code> element on your webpage</p>
                    <code style="display:block;" @click="copy_text(data.inline_js_code)">{{data.inline_js_code}}</code>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Embed Installation</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Instruction</h6>
                    <p class="card-text">Please copy the code below and place in the body of your webpage</p>
                    <code style="display:block;" @click="copy_text(data.embed_code)">{{data.embed_code}}</code>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .checkbox {
        position: relative;
        top: 4px;
    }
</style>
<script>
    const {
        createApp
    } = Vue
    // Vue.prototype.appName = 'MyApp';


    createApp({

        methods: {
            copy_text(text) {
                copy_text(text)
            },
            async saveChanges() {


                let re = await fetch(`${$base_url}/user/update_applet`, {
                        body: JSON.stringify(this.applet),
                        method: "post"
                    })
                    .then(response => (response.json()));

                notify();
            },

            invert_selection($event, position = 'destination_bookies') {

                for (let i in this.data[position]) {
                    let bookie = this.data[position][i];
                    if (this.applet.details[position] == undefined) {
                        this.applet.details[position] = {}
                    }

                    if (this.applet.details[position][bookie.bookie] == true) {
                        delete this.applet.details[position][bookie.bookie];
                    } else {

                        this.applet.details[position][bookie.bookie] = true;
                    }
                }
            },

            select_all_bookies($event, position = 'destination_bookies') {

                if (!$event.target.checked) {
                    this.applet.details[position] = {}
                    return;
                }



                for (let i in this.data[position]) {
                    let bookie = this.data[position][i];
                    if (this.applet.details[position] == undefined) {
                        this.applet.details[position] = {}
                    }

                    if ($event.target.checked) {

                        this.applet.details[position][bookie.bookie] = true;
                    } else {
                        delete this.applet.details[position][bookie.bookie];

                    }

                }
            },

            is_bookie_selected(bookie, position = 'destination_bookies') {
                if (this.applet.details[position] == undefined) {
                    return false;
                }


                return (this.applet.details[position][bookie.bookie] == true)
            },


            select_bookie(bookie, position = 'destination_bookies') {
                if (this.applet.details[position] == undefined) {
                    this.applet.details[position] = {}
                }
                if (this.applet.details[position][bookie.bookie]) {
                    delete this.applet.details[position][bookie.bookie];
                } else {
                    this.applet.details[position][bookie.bookie] = true;
                }
            }
        },

        async created() {

            const url = new URL(window.location.href);
            let id = url.searchParams.get('id');

            let data = await fetch(`${$base_url}/user/get_applet/${id}`)
                .then(response => (response.json()))

            this.applet = data.applet;
            this.bookies = data.bookies;
            this.data = data;
        },

        computed: {

            fola: function() {
                return `${this.message} is grea;`
            }
        },

        data() {


            return {
                window: window,
                message: 'Hello Vue!',
                bookies: {},
                available_bookies: {},
                applet: {
                    details: {}
                },
            }
        }





    }).mount('#app')
</script>
<?php include_once 'includes/footer.php'; ?>