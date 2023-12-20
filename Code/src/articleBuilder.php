<?php
require_once("dbConnect.php");
require('tfpdf/tfpdf.php');

class buildEdition {
    private $pdf;

    public function __construct($editionId, $filename) {
        $editionData = $this->getEdition($editionId);

        $editionArticles = $this->getArticles($editionId);

        $this->pdf = new tFPDF('P', 'mm', 'A4');

        $this->pdf->AddFont('DejaVu','B','DejaVuSans-Bold.ttf',true);
        $this->pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);

        $this->buildMainPage($editionData["title"], $editionData["date"]);

        $this->writeArticles($editionArticles);

        $this->writeFile($filename);
    }

    private function buildMainPage($editionName, $editionDate) {
        $this->pdf->AddPage();

        $imagePath = __DIR__ . "/../assets/Images/logo-full-transparent.png";

        list($width, $height) = getimagesize($imagePath);

        $maxWidth = 150;
        $maxHeight = 100;

        if ($width > $height) {
            $newWidth = $maxWidth;
            $newHeight = ($maxWidth / $width) * $height;
        } 
        
        else {
            $newHeight = $maxHeight;
            $newWidth = ($maxHeight / $height) * $width;
        }

        $marginLeft = ($this->pdf->GetPageWidth() - $newWidth) / 2;
        $this->pdf->Image($imagePath, $marginLeft, 10, $newWidth, $newHeight);

        $this->pdf->SetY($newHeight + 30);
        $this->pdf->SetFont('DejaVu','B', 30);
        $this->pdf->MultiCell(0, 10, $editionName, 0, 'C');

        $this->pdf->SetFont('Arial', 'B', 18);
        $textYPosition = $this->pdf->GetPageHeight() - 70;

        $text = $editionDate;
        $this->pdf->SetY($textYPosition);
        $this->pdf->MultiCell(0, 40, $text, 0, 'C');

    }

    private function writeArticles($articles) {
        $this->pdf->AddPage();

        foreach($articles as $article) {
            $this->writeArticle($article);
        }
    }

    private function writeArticle($article) {
        $this->pdf->SetFont('DejaVu','B',12);
        $this->pdf->Cell(0, 10, $article["title"], 0, 1, 'C');
        $this->pdf->Ln(10);

        $this->pdf->SetFont('DejaVu', '', 12);
        $this->pdf->MultiCell(0, 10, str_replace("&nbsp;", "", strip_tags($article["text"])));

        $this->pdf->SetFont('DejaVu', '', 12);
        $this->pdf->Cell(0, 10, 'Author: ' . $article["full_name"], 0, 1, 'R');

    }

    private function getEdition($editionId) {
        try {
            $edition = Db::queryOne("SELECT * FROM editions WHERE editionID = ?", $editionId);
        }

        catch(PDOException $e) {
            throw new Exception("Nepodařilo se získat vydání z databáze");
        }

        if(empty($edition)) {
            throw new Exception("Vydání nebylo nalezeno");
        }

        return $edition;
    }

    private function getArticles($editionId) {
        try {
            $articles = Db::queryAll("
                SELECT articles.*, CONCAT(users.firstname, \" \", users.lastname) AS full_name
                FROM article_edition 
                RIGHT JOIN articles ON articles.articleID = article_edition.article
                INNER JOIN users ON articles.author = users.userID
                WHERE article_edition.editionID = ?
                ORDER BY article_edition.order
            ", $editionId);
        }

        catch(PDOException $e) {
            throw new Exception("Unable to fetch articles");
        }

        if(empty($articles)) {
            throw new Exception("Vydání neobsahuje žádné články");
        }

        return $articles;
    }

    private function writeFile($filename) {
        if(file_exists(__DIR__ . "/../assets/editions/") == false) {
            if(is_writable(__DIR__ . "/../assets/") == false) {
                throw new Exception("Cannot write to directory");
            }

            mkdir(__DIR__ . "/../assets/editions/");
        }

        $this->pdf->Output('F', __DIR__ . "/../assets/editions/" . $filename);
    }
}