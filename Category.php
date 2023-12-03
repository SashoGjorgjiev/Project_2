<?php

class Category
{
    private $category_id;
    private $title;
    private $deleted_at;

    public function __construct($category_id, $title, $deleted_at)
    {
        $this->category_id = $category_id;
        $this->title = $title;
        $this->deleted_at = $deleted_at;
    }

    public function getCategoryID()
    {
        return $this->category_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function softDelete()
    {
        $this->deleted_at = date('Y-m-d H:i:s');
    }
}
