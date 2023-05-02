<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Template;
use App\Lib\Fpdf\PDF;
use App\Http\Resources\TemplateResource;

class TemplateController extends Controller
{
	/**
     * Handle an incoming load template request.
     *
     * @param  \App\Http\Requests\Admin\CreatePackageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $templates = Template::get();
		return TemplateResource::collection($templates);
    }
	
	/**
     * Handle an incoming load template request.
     *
     * @param  \App\Http\Requests\Admin\CreatePackageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function load(Request $request)
    {
        $template = Template::where('uuid', $request->uuid)->first();
		return response()->json(['name' => $template->name, 'id' => $template->uuid, 'page_settings' => $template->page_settings, 'draggables' => $template->draggables], 200);
    }
	
    /**
     * Handle an incoming create plan request.
     *
     * @param  \App\Http\Requests\Admin\CreatePackageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
		$user = $request->user();
		$templateArray = $request->template;
		$userID = 1;//$user->id;
        Template::create([
						'owner_id' => $userID,
						'uuid' => md5(time().$userID),
						'name' => $templateArray['name'],
						'page_settings' => $templateArray['page_settings'],
						'draggables' => $templateArray['draggables'],
					]);
		return response()->json(['message' => "Template has been saved successfully."], 200);
    }
	
	/**
     * Handle an incoming create plan request.
     *
     * @param  \App\Http\Requests\Admin\CreatePackageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
		$templateArray = $request->template;
        $template = Template::where('uuid', $request->id)->first();
		$template->page_settings = $templateArray['page_settings'];
		$template->draggables = $templateArray['draggables'];
		$template->save();
		return response()->json(['message' => "Template has been updated successfully."], 200);
    }
	
	public function preview(Request $request) {
		new PDF($request->id);
	}
}
