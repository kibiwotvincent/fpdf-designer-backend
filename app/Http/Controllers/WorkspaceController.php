<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Setting;
use App\Models\Document;
use App\Http\Resources\WorkspaceResource;

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
										]);
										
		return new WorkspaceResource($workspace);
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
			$name = null;
			$source = 'templates';
			$templateID = $data->uuid;
		}
		else {
			$data = Document::where('uuid', $request->uuid)->first();
			$name = $data->name;
			$source = 'documents';
			$templateID = null;
		}
		
		//delete existing workspace
		Workspace::where('uuid', $data->uuid)->delete();
		
		//create a workspace with default page settings
        $workspace = Workspace::create([
										'uuid' => $data->uuid,
										'name' => $name,
										'page_settings' => $data->page_settings,
										'draggables' => $data->draggables,
										'source' => $source,
										'template_id' => $templateID,
										]);
										
		return new WorkspaceResource($workspace);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function save(Request $request)
    {
		$workspaceArray = $request->document;
        $workspace = Workspace::where('uuid', $request->uuid)->first();
		$workspace->name = $workspaceArray['name'];
		$workspace->page_settings = $workspaceArray['page_settings'];
		$workspace->draggables = $workspaceArray['draggables'];
		$workspace->save();
		//save into documents table
		if($workspace->source == 'documents') {
			//update existing record
			$document = Document::where('uuid', $workspace->uuid)->first();
			$document->page_settings = $workspaceArray['page_settings'];
			$document->draggables = $workspaceArray['draggables'];
			$document->save();
		}
		else {
			//create new record
			$user = $request->user();
			$documentArray = $request->document;
			$document = Document::create([
							'user_id' => $user->id,
							'uuid' => $workspace->uuid,
							'name' => $workspaceArray['name'],
							'page_settings' => $workspaceArray['page_settings'],
							'draggables' => $workspaceArray['draggables'],
						]);
		}
		return new WorkspaceResource($workspace);
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
	
}