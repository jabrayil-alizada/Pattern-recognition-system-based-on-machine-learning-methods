<?php

namespace App\Service;

use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\TestQuestion;
use App\Models\TestQuestionAnswer;
use App\Util\Errors\ErrorCode;
use App\Util\Helper\Generator;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class TensorflowService {

    const IMAGES_DIR_PATH = "/classificator/images_to_classify/";

    const MODEL_IMAGES_PATH = "/var/www/html/all_classes";

    public function addNewClass(string $className)
    {
        return mkdir(self::MODEL_IMAGES_PATH . "/$className");
    }

    public function listAllClasses()
    {
        $classes = scandir(self::MODEL_IMAGES_PATH);
        unset($classes[0]);
        unset($classes[1]);

        $classes = array_values($classes);

        return $classes;
    }

    public function retrainModel()
    {
        $scriptStr = "python3 /classificator/scripts/retrain.py --output_graph=/classificator/tf_files/retrained_graph.pb --output_labels=/classificator/tf_files/retrained_labels.txt --image_dir=/classificator/tf_files/animals";

        $result = exec($scriptStr);

        return $result ? true : false;
    }

    public function classifyImage(string $imgName)
    {
        $scriptStr = "python scripts/label_image.py --image " . static::IMAGES_DIR_PATH . $imgName;

        $result = exec($scriptStr);

        $arr = $this->_strResultToArray($result);

        return $arr;
    }

    protected function _strResultToArray(string $str) {
        $explodedStr = explode(PHP_EOL, $str);

        $startRow = 5;
        $totalClassifyingClasses = 5;
        $result = [];

        for ($i = $startRow; $i < ($startRow + $totalClassifyingClasses); $i++)
        {
            $row = explode(" ", $explodedStr[$i]);
            $class = $row[0];
            $score = $row[1];

            preg_match("/\d+\.\d+\)$/", $score, $matches);

            $classificationResult = str_replace(")", "", $matches[0]);

            $result[$class] = $classificationResult * 100;
        }

        arsort($result);

        $bestMatch = [
            array_key_first($result),
            $result[array_key_first($result)],
        ];


        return [
            "best_match_class" => $bestMatch[0],
            "best_match_percent" => $bestMatch[1],
            "all" => $result
        ];
    }

}
