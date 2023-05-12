<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Lib\Fpdf\PDF;
use App\Http\Resources\TemplateResource;
use App\Http\Requests\RenameTemplateRequest;
use App\Events\DocumentDeleted;

class TemplateController extends Controller
{
	/**
     * Fetch available templates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $templates = Template::get();
		return TemplateResource::collection($templates);
    }
	
	/**
     * Handle an incoming load template request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function load(Request $request)
    {
        $template = Template::where('uuid', $request->uuid)->first();
		return response()->json(['name' => $template->name, 'id' => $template->uuid, 'page_settings' => $template->page_settings, 'draggables' => $template->draggables], 200);
    }
	
	/**
     * Handle an incoming rename template request.
     *
     * @param  App\Http\Requests\RenameTemplateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renameTemplate(RenameTemplateRequest $request)
    {	
		$template = Template::where('uuid', $request->uuid)->first();
		$template->name = $request->name;
		$template->save();
		return response()->json(['message' => "Template has been renamed successfully."], 200);
	}
	
	/**
     * Create template pdf on the fly and force browser to download.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function viewPdf(Request $request) {
		$template = Template::where('uuid', $request->uuid)->first();
		new PDF($template);
	}
	
	/**
     * Handle an incoming delete template request.
     *
     * @param  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {	
		Template::where('uuid', $request->uuid)->delete();
		/*
		* Don't fire DocumentDeleted event that will cause file removal &
		* permanent template deletion ***for now***
		* 
		$template = Template::withTrashed()->where('uuid', $request->uuid)->first();
		DocumentDeleted::dispatch($template);
		*/
		return response()->json(['message' => "Template has been deleted successfully."], 200);
	}
}
