<?php

namespace Modules\Page\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Page\Models\Page;
use Modules\Page\Transformers\PageResource;
use Modules\Page\Transformers\FaqResource;
use Modules\FAQ\Models\FAQ;


class PagesController extends Controller
{
  public function __construct()
  {
  }

  public function pageList(Request $request)
  {
      $perPage = $request->input('per_page', 10);

      $page =  Page::where('status',1);

      $page = $page->paginate($perPage);
      $items = PageResource::collection($page);
      return response()->json([
          'status' => true,
          'data' => $items,
          'message' => __('messages.page_list'),
      ], 200);
  }
  public function faqList(Request $request)
  {
      $perPage = $request->input('per_page', 10);

      $faq =  FAQ::where('status',1);   
      $faq = $faq->paginate($perPage);
      $items = FaqResource::collection($faq);
      return response()->json([
          'status' => true,
          'data' => $items,
          'message' => __('messages.faq_list'),
      ], 200);
  }
}
