<?php
namespace Konnect\NayaFramework\Lib;

class View {
    protected static string $viewPath ='views';


    // Render the PHP template view
    public static function render($viewName, $data = []): void
    {
        // Extract the data array into variables
        extract($data);

        // Check if the view file exists
        $viewFile = "../". self::$viewPath . '/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            // Include the view file and render it
            include $viewFile;
        } else {
            // If the view file doesn't exist, show an error
            echo "View '$viewName' not found.";
        }
    }
}
