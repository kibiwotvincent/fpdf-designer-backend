<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Document;

class DocumentController extends Controller
{
	/**
     * Handle an incoming load document request.
     *
     * @param  \App\Http\Requests\Admin\CreatePackageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $documents = Document::get();
		return response()->json($documents, 200);
    }
	
	/**
     * Handle an incoming load document request.
     *
     * @param  \App\Http\Requests\Admin\CreatePackageRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function load(Request $request)
    {
        $document = Document::where('uuid', $request->id)->first();
		return response()->json(['draggables' => $document->draggables], 200);
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
		Log::info($request->document);
		$user = $request->user();
		$documentArray = $request->document;
		$userID = 1;//$user->id;
        Document::create([
						'user_id' => $userID,
						'uuid' => md5(time().$userID),
						'name' => $documentArray['name'],
						'page' => $documentArray['page'],
						'draggables' => $documentArray['draggables'],
					]);
		return response()->json(['message' => "Document has been saved successfully."], 200);
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
		$documentArray = $request->document;
        $document = Document::where('uuid', $request->id)->first();
		$document->page = $documentArray['page'];
		$document->draggables = $documentArray['draggables'];
		$document->save();
		return response()->json(['message' => "Document has been updated successfully."], 200);
    }
}
