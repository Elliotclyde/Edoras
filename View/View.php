<?php 

class View
{

    private $viewsDir;
    private $fileType;
    private $filePath;
    private $variables;

    public function __construct($viewName, $variables = [])
    {
        $this->viewsDir= $_SERVER['DOCUMENT_ROOT'] . "/../Views/";
        $this->viewName = $viewName;
        $this->variables = $variables;
        $this->filePath = $this->getFilePath();
        $this->fileType = $this->getFileType();
    }

    public function make()
    {
        return $this->getViewContents();
    }

    private function getViewContents()
    {
        // if php return php output
        if ($this->fileType == 'php') {
            return $this->getPHPViewOutput();
        }

        // if non php file return contents
        return (file_get_contents($this->filePath));
    }

    private function getFilePath()
    {
        $ViewFilePathMatches = $this->getMatchingViewFiles($this->viewName);

        if (count($ViewFilePathMatches) === 0) {
            throw new Exception('no matching view filenames in views directory for ' . $this->viewName);
        }
        return $this->viewsDir . reset($ViewFilePathMatches);
    }

    private function getFileType()
    {
        return explode('.', $this->filePath)[3];
    }

    private function getMatchingViewFiles($viewName)
    {
        return array_filter(
            scandir($this->viewsDir),
            function ($filePath) use ($viewName) {
                return (explode('.', $filePath)[0] === $viewName);
            });
    }
    private function getPHPViewOutput()
    {
        
        $output = null;

        if (file_exists($this->filePath)) {
            extract($this->variables);
            ob_start();
            include $this->filePath;
            $output = ob_get_clean();
        }

        return $output;
    }


}
