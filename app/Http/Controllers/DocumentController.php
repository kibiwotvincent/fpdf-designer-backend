<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Document;
use App\Http\Resources\DocumentResource;
use App\Http\Requests\RenameDocumentRequest;
use App\Events\DocumentDeleted;

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
        $documents = $request->user()->documents;
		return DocumentResource::collection($documents);
    }
	
	/**
     * Handle an incoming rename saved document request.
     *
     * @param  App\Http\Requests\RenameDocumentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function renameDocument(RenameDocumentRequest $request)
    {	
		$document = Document::where('uuid', $request->uuid)->first();
		$document->name = $request->name;
		$document->save();
		return response()->json(['message' => "Document has been renamed successfully."], 200);
	}
	
	/**
     * Create document pdf on the fly and force browser to download.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
	public function viewPdf(Request $request) {
		$document = Document::where('uuid', $request->uuid)->first();
		$document->previewPdf();
	}
	
	/**
     * Handle an incoming delete saved document request.
     *
     * @param  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {	
		$document = Document::where('uuid', $request->uuid)->first();
		$this->authorize('delete', $document);
		
		$document->delete();
		DocumentDeleted::dispatch($document);
		return response()->json(['message' => "Document has been deleted successfully."], 200);
	}
}
