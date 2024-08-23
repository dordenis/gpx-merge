<?php

class File {

    /**
     * @param string $from
     * @return \SplFileInfo[]
     */
    public function getFiles(string $from): array
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($from));
        $result = [];

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }
            $result[] = $file;
        }
        return $result;
    }

    private function createDir(\SplFileInfo $file): string
    {
        $year = date("Y", $file->getMTime());
        $distinct = "{$this->toFolderImg}/{$year}/";
        @mkdir($distinct, 0777, true);
        return $distinct;
    }


    public function copy()
    {
        $files = $this->getFiles($this->fromFolderImg);

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $name = date("Y-m-d_H:i:s", $file->getMTime());
            $distinct = $this->createDir($file).$name.'.'.$file->getExtension();
            @copy($file->getPathname(), $distinct);
        }
    }

    public function insert()
    {
        $images = $this->getFiles($this->toFolderImg);
        $rest = new Rest($images);

        foreach ($rest->listPages() as $page) {
            $distinct = $this->createDirMd($page->getDate());
            $page = $this->setFileMd($page);

            $content = $page->content();
            $filename = $distinct.$page->getFileName();
            file_put_contents($filename, $content);
        }

    }

    private function createDirMd($date): string
    {
        $year = preg_replace("/(\d{4})(.+)/", '$1/', $date);
        $distinct = $this->toFolderRest.$year;
        @mkdir($distinct, 0777, true);
        return $distinct;
    }

    private function setFileMd(RestPage $page): RestPage
    {
        $pattern = $this->fromFolderRest.$page->getYear().$page->getDate()."*.md";
        $files = glob($pattern);
        if (count($files) > 0) {
            $page->setPathname($files[0]);

        }
        return $page;
    }
}

