<?php


/**
 *
 */
class PagesController extends controller
{


    public function __construct()
    {
    }


    public function why_conversions_may_fail()
    {
        $this->view('guest/why_conversions_may_fail');
    }


    public function applet()
    {
        $this->view('guest/applet');
    }

    public function conversion_link()
    {
        $this->view('guest/conversion_link');
    }

    public function index()
    {
        $this->view('guest/about_us');
    }

    public function add_testimonial()
    {
        $testimony = new Testimonials;

        $this->view('guest/add_testimonial', get_defined_vars());
    }


    public function api()
    {

        $this->view('guest/api');
    }

    public function pricing()
    {
        $this->view('guest/pricing2');
    }


    public function privacy_policy()
    {

        $this->view('guest/privacy_policy');
    }




    public function affiliate_agreement()
    {
        $this->view('guest/affiliate_agreement');
    }
    public function terms_of_service()
    {
        $this->view('guest/terms_of_service');
    }

    public function cookie_policy()
    {
        $this->view('guest/cookie_policy');
    }

    public function disclaimer()
    {
        $this->view('guest/disclaimer');
    }


    public function contact_us()
    {
        $this->view('guest/contact_us');
    }


    public function about_us()
    {
        $this->view('guest/about_us');
    }


    public function faqs()
    {
        $this->view('guest/faqs');
    }

    public function reviews()
    {
        $this->view('guest/reviews');
    }
    public function affiliate()
    {
        $this->view('guest/affiliate');
    }
    public function landingpage()
    {
        $this->view('guest/landingpage');
    }

    public function ads_redirect()
    {

        $link = $_GET['link'];
        Redirect::to($link);
        // $this->view('guest/ads_redirect');
    }
}
