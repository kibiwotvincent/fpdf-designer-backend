<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Setting;
use App\Models\Document;
use App\Models\Template;
use App\Http\Resources\WorkspaceResource;
use App\Events\DocumentSaved;
use App\Events\UpdatingDocument;
use App\Facades\PDF;
use App\Http\Requests\SaveDocumentRequest;
use Auth;

class WorkspaceController extends Controller
{
	/**
     * Handle an incoming initiate blank workspace request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function initBlank(Request $request)
    {
		//get settings
		$settings = Setting::get()->mapWithKeys(function ($setting) {
			return [$setting['config'] => $setting['value']];
		});
		
		/*convert page settings from json to array since it'll automatically be cast to json before saving*/
		$pageSettings = json_decode($settings['page_defaults']);
		
		//create a workspace with default page settings
        $workspace = Workspace::create([
										'uuid' => md5(time().rand(111111, 999999)),
										'page_settings' => $pageSettings,
										'draggables' => [],
										'save_as' => $request->saveAs == "" ? "document" : $request->saveAs,
										]);
										
		$setup['fonts'] = json_decode($settings['fonts']);
		$setup['page_sizes'] = json_decode($settings['page_sizes']);
		$setup['page_margins'] = json_decode($settings['page_margins']);
		$defaults['text'] = json_decode($settings['text_defaults']);
		$defaults['page'] = json_decode($settings['page_defaults']);
		$defaults['rectangle'] = json_decode($settings['rectangle_defaults']);
		return response()->json(['id' => $workspace->uuid, 'setup' => $setup, 'defaults' => $defaults], 200);
    }
	
	/**
     * initiate blank workspace from source - either a template or saved document
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function initFromSource(Request $request)
    {
		//get settings
		$settings = Setting::get()->mapWithKeys(function ($setting) {
			return [$setting['config'] => $setting['value']];
		});
		
		$source = $request->source;
		if($source == 'templates') {
			$data = Template::where('uuid', $request->uuid)->first();
			$name = $request->saveAs == "template" ? $data->name : null;
			$uuid = $request->saveAs == "template" ? $data->uuid : md5(time().rand(111111, 999999));
			$source = 'templates';
			$templateID = $data->uuid;
		}
		else {
			$data = Document::where('uuid', $request->uuid)->first();
			$name = $data->name;
			$uuid = $data->uuid;
			$source = 'documents';
			$templateID = null;
		}
		//delete existing workspace of the same document
		Workspace::where('uuid', $data->uuid)->delete();
		
		//create a workspace with duplicate data from source
        $workspace = Workspace::create([
										'uuid' => $uuid,
										'name' => $name,
										'page_settings' => $data->page_settings,
										'draggables' => $data->draggables,
										'source' => $source,
										'template_id' => $templateID,
										'save_as' => $request->saveAs == "" ? "document" : $request->saveAs,
										]);
										
		$setup['fonts'] = json_decode($settings['fonts']);
		$setup['page_sizes'] = json_decode($settings['page_sizes']);
		$setup['page_margins'] = json_decode($settings['page_margins']);
		$defaults['text'] = json_decode($settings['text_defaults']);
		$defaults['page'] = json_decode($settings['page_defaults']);
		$defaults['rectangle'] = json_decode($settings['rectangle_defaults']);
		return response()->json(['id' => $workspace->uuid, 'setup' => $setup, 'defaults' => $defaults], 200);
    }
	
	/**
     * Fetch saved workspace whose uuid matches the passed uuid
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function load(Request $request)
    {
		$workspace = Workspace::where('uuid', $request->uuid)->first();
		return new WorkspaceResource($workspace);
    }
	
	/**
     * Handle an incoming document update request.
     *
     * @param  App\Http\Requests\SaveDocumentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function save(SaveDocumentRequest $request)
    {	
        $workspace = Workspace::where('uuid', $request->uuid)->first();
		$workspace->name = $request->name;
		$workspace->page_settings = $request->page_settings;
		$workspace->draggables = $request->draggables;
		$workspace->save();
		
		if($workspace->save_as == "template") {
			$this->saveAsTemplate($workspace);
		}
		else {
			$this->saveAsDocument($workspace);
		}
		
		return new WorkspaceResource($workspace);
    }
	
	/**
     * Save workspace to documents table.
     * This means the workspace is user document under design
     *
     * @param  App\Models\Workspace  $request
     * @return void
     */
	private function saveAsDocument(Workspace $workspace) {
		$pageSettings = $workspace->page_settings;
		$draggables = $workspace->draggables;
		$thumbnail = md5(time().rand(111111, 999999)).".png";
		
		//save into documents table
		$document = Document::where('uuid', $workspace->uuid)->first();
		if(isset($document->id)) {
			//fire an event that will delete existing document thumbnail
			UpdatingDocument::dispatch($document);
			
			//update existing record
			$document->page_settings = $pageSettings;
			$document->draggables = $draggables;
			$document->thumbnail = $thumbnail;
			$document->save();
		}
		else {
			//create new record
			$user = request()->user();
			$document = Document::create([
							'user_id' => $user->id,
							'uuid' => $workspace->uuid,
							'name' => $workspace->name,
							'page_settings' => $pageSettings,
							'draggables' => $draggables,
							'thumbnail' => $thumbnail,
						]);
		}
		DocumentSaved::dispatch($document);
	}
	
	/**
     * Save workspace to templates table.
     * This means admin is adding/updating template
     *
     * @param  App\Models\Workspace  $request
     * @return void
     */
	private function saveAsTemplate(Workspace $workspace) {
		$pageSettings = $workspace->page_settings;
		$draggables = $workspace->draggables;
		$thumbnail = md5(time().rand(111111, 999999)).".png";
		
		//save into templates table
		$uuID = $workspace->save_as == "template" ? $workspace->template_id : $workspace->uuid;
		$template = Template::where('uuid', $uuID)->first();
		if(isset($template->id)) {
			//fire an event that will delete existing template thumbnail
			UpdatingDocument::dispatch($template);
			
			//update existing record
			$template->page_settings = $pageSettings;
			$template->draggables = $draggables;
			$template->thumbnail = $thumbnail;
			$template->save();
		}
		else {
			//create new record
			$user = request()->user();
			$template = Template::create([
							'user_id' => $user->id,
							'uuid' => $workspace->uuid,
							'name' => $workspace->name,
							'page_settings' => $pageSettings,
							'draggables' => $draggables,
							'thumbnail' => $thumbnail,
						]);
		}
		DocumentSaved::dispatch($template);
	}
	
	/**
     * Handle an incoming document reset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function reset(Request $request)
    {
		$workspaceID = $request->uuid;
        $workspace = Workspace::where('uuid', $workspaceID)->first();
		if($workspace->source == 'templates') {
			$template = Template::where('uuid', $workspaceID)->first();
			$pageSettings = $template->page_settings;
			$draggables = $template->draggables;
		}
		elseif($workspace->source == 'documents') {
			$document = Document::where('uuid', $workspaceID)->first();
			$pageSettings = $document->page_settings;
			$draggables = $document->draggables;
		}
		else {
			$pageSettings = json_decode((Setting::where('config', 'page_defaults')->first())->value);
			$draggables = [];
		}
		$workspace->page_settings = $pageSettings;
		$workspace->draggables = $draggables;
		$workspace->save();
		return new WorkspaceResource($workspace);
    }
	
	/**
     * Handle an incoming document preview request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
	public function preview(Request $request) {
		$workspace = Workspace::where('uuid', $request->uuid)->first();
		
		if($workspace->save_as == "template") {
			$workspace = Template::where('uuid', $workspace->template_id)->first();
		}
		else {
			$workspace = Document::where('uuid', $workspace->uuid)->first();
		}
		
		PDF::preview($workspace);
	}
}
