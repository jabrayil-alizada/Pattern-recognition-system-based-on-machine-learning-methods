<?php

namespace App\Http\Controllers;

use App\Service\TensorflowService;
use App\Service\TestService;
use App\Util\Errors\ErrorCode;
use App\Util\Helper\CheckUser;
use Illuminate\Http\Request;

class TensorflowController extends Controller
{

    /**
     * int num represents percent
     */
    const RECOGNITION_THRESHOLD = 80;

    const MODEL_IMAGES_PATH = "/var/www/html/all_classes";

    protected $_tensorflowService;

    public function __construct(
        TensorflowService $tensorflowService
    )
    {
        $this->_tensorflowService = $tensorflowService;
    }

    public function listAllClasses(Request $request)
    {
        return response()->json($this->_tensorflowService->listAllClasses());
    }

    public function addNewClass(Request $request)
    {
        $className = $request->input("class_name");
        $allClasses = $this->_tensorflowService->listAllClasses();

        if(!$className)
        {
            return response()->json([
                "data" => "Class name must present in request"
            ]);
        }

        if(in_array($className, $allClasses))
        {
            return response()->json([
                "data" => "Class with this name already exists"
            ]);
        }

        $result = $this->_tensorflowService->addNewClass($className);

        if($result)
        {
            return response()->json([
                "data" => "Class with name: $className was successfully added"
            ]);
        }

        return response()->json([
            "error" => "Class cant be added, error"
        ]);
    }

    public function addImageToClass(Request $request)
    {
        $allClasses = $this->_tensorflowService->listAllClasses();

        $className = $request->input('class_name');

        if(!$className)
        {
            return response()->json([
                "data" => "Class name must present in request"
            ]);
        }

        if(!in_array($className, $allClasses))
        {
            return response()->json([
                "data" => "Class with name $className not found"
            ]);
        }

        if($request->file('image_path')) {
            request()->validate([
                'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = time() . '.' . request()->image_path->getClientOriginalExtension();

            request()->image_path->move(self::MODEL_IMAGES_PATH . "/" . $className, $imageName);

            return response()->json([
                "error" => "Image successfully added to class"
            ]);
        }

        return response()->json([
            "error" => "Class cant be added, error"
        ]);
    }

    public function classify(Request $request)
    {
        $testImage = $request->input("test_image");
        $recognitionThreshold = $request->input("recognition_threshold") ? $request->input("recognition_threshold") : self::RECOGNITION_THRESHOLD;

        $path = dirname(__FILE__, 4) . "/public/test_images/";

        if(!$testImage || !file_exists($path. $testImage))
        {
            return response()->json(["data" => "No image to recognize"]);
        }

        $result = $this->_tensorflowService->classifyImage("$testImage");

        $data["recognition_result"] = false;

        if($result["best_match_percent"] > $recognitionThreshold)
        {
            $data["recognition_result"] = true;
        }

        $data["info"] = $result;

        return response()->json([
            "data" => $data
        ]);
    }
}
