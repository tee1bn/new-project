<?php

use v2\Models\Wp\Post;
use v2\Filters\Filters\PostFilter;


/**
 *
 */
class BlogController extends controller
{


    public $recent_posts;
    public $categories;
    public $per_page;


    public function __construct()
    {

        $this->recent_posts = Post::Recent()->take(5)->get();
        $this->categories = Post::categories();
        $this->per_page = 50;
    }



    public function category()
    {

        $sieve = ['category' => func_get_arg(0)];
        $query = Post::Published();


        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $this->per_page = 50;
        $skip = (($page - 1) * $this->per_page);

        $filter = new PostFilter($sieve);

        $data = $query->Filter($filter)->count();

        $posts = $query->Filter($filter)
            ->offset($skip)
            ->take($this->per_page)
            ->get();  //filtered


        $this->view('guest/blog', get_defined_vars());
    }

    public function index()
    {


        $sieve = [];
        switch (func_num_args()) {
            case 1:
                // year
                $sieve['year'] = date("Y", strtotime(implode("-", array_slice(func_get_args(), 0, 3))));
                break;

            case 2:
                // month
                $sieve['month'] = date("Y-m", strtotime(implode("-", array_slice(func_get_args(), 0, 3))));
                break;

            case 3:
                // date
                $sieve['date'] = date("Y-m-d", strtotime(implode("-", array_slice(func_get_args(), 0, 3))));
                break;

            case  4 || 5:
                //id is present
                $id = str_replace("id-", "", func_get_arg(3));
                $post = Post::find($id);

                $this->recent_posts = Post::Recent($post)->take(5)->get();

                $blog_page = 'single';
                $this->view('guest/blog', get_defined_vars());

                return;
                break;
        }




        $query = Post::Published();
        $posts = $query->get();


        $this->view('guest/blog', get_defined_vars());
    }
}
