<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class OffersController extends Controller
{
  public function Offers_list()
  {
    try {
      $page_title = 'Hotels List';
      $page_description = '';
      $breadcrumbs = [
        [
          'title' => 'Offers_list',
          'url' => '',
        ]
      ];

      $status = 1;
      $users = User::with(['role'])->when($status, function ($users) use ($status) {
        if ($status != '-1') {
          $status = conditionalStatus($status);
          $users->where('status', '=', $status);
        }
      })->orderBy('id', 'desc')->get();

      //   dd($users);
      return view('admin.pages.offers.list', compact('page_title', 'page_description', 'breadcrumbs',  'users'));
    } catch (\Exception $e) {
      dd($e);
      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  public function add_offers()
  {
    try {
      $page_title = 'Add Offer';
      $page_description = '';
      $breadcrumbs = [
        [
          'title' => 'Add Offer',
          'url' => '',
        ]
      ];
      return view('admin.pages.offers.add', compact('page_title', 'page_description', 'breadcrumbs'));
    } catch (\Exception $e) {
      dd($e);
      return redirect()->back()->with('error', $e->getMessage());
    }
  }
}
