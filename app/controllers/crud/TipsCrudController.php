<?php

use Illuminate\Database\Capsule\Manager as DB;
use v2\Models\Tip;

/**
 *
 */
class TipsCrudController extends controller
{

    public function __construct()
    {

        if (!$this->admin()) {
            $this->middleware('current_user')
                ->mustbe_loggedin();
        }
    }



    public function toggle_failed_state($ad_id)
    {
      /*  if (!$this->admin()) {
            return;
        }
*/
        

        $ad =   Tip::find($ad_id);

        if ($ad == null) {
            Session::putFlash('danger', "Tip does not exist");
            return;
        }

        try {

            if ($ad->is_failed()) {

                $ad->update([
                    'failed_date' => null
                ]);

                Session::putFlash('success', "<b>{$ad->paper->name}<b> marked as ongoing successfully");
            } else {

                $ad->update([
                    'failed_date' => date("Y-m-d H:i:s")
                ]);

                Session::putFlash('success', "<b>{$ad->paper->name}<b> marked as failed successfully");
            }
        } catch (Exception $e) {
            Session::putFlash('danger', "Something went wrong");
        }
    }
}
