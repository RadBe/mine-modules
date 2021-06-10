<?php

$pages = '';

if ($this->currentPage > 1) {
    $pages .= '<li><a href="' . $this->createUrl($this->currentPage - 1) . '">&lt;&lt;</a></li>';
}

if ($this->countPages <= 10) {
    for ($i = 1; $i <= $this->countPages; $i++)
    {
        if ($this->currentPage != $i) {
            $pages .= '<li><a href="' . $this->createUrl($i) . '">' . $i . '</a></li>';
        } else {
            $pages .= '<li class="active"><span>' . $i . '</span></li>';
        }
    }
} else {
    $start = 1;
    $end = 10;

    if ($this->currentPage > 4) {
        $start = $this->currentPage - 3;
        $end = $start + 8;

        if ($end > $this->countPages) {
            $start = $this->countPages - 10;
            $end = $this->countPages;
        }
    }

    if ($start > 1) {
        $pages .= '<li><a href="' . $this->createUrl(1) . '">1</a></li><li><span>...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++)
    {
        if ($this->currentPage != $i) {
            $pages .= '<li><a href="' . $this->createUrl($i) . '">' . $i . '</a></li>';
        } else {
            $pages .= '<li class="active"><span>' . $i . '</span></li>';
        }
    }

    if ($this->countPages - $this->currentPage > 5) {
        $pages .= '<li><span>...</span></li><li><a href="' . $this->createUrl($this->countPages) . '">' . $this->countPages . '</a></li>';
    }
}

if ($this->currentPage < $this->countPages) {
    $pages .= '<li><a href="' . $this->createUrl($this->currentPage + 1) . '">&gt;&gt;</a></li>';
}

return "<ul class=\"pagination pagination-sm\">$pages</ul>";
