<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Document;
use App\Lib\Fpdf\PDF;
use App\Http\Resources\DocumentResource;

class DocumentController extends Controller
{
	/**
     * Fetch user documents.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $documents = Document::get();
		return DocumentResource::collection($documents);
    }
	
	/**
     * Handle an incoming update document request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
		$documentArray = $request->document;
        $document = Document::where('uuid', $request->id)->first();
		$document->page_settings = $documentArray['page_settings'];
		$document->draggables = $documentArray['draggables'];
		$document->save();
		return response()->json(['message' => "Document has been updated successfully."], 200);
    }
	
	public function download(Request $request) {
		new PDF($request->id);
	}
}
