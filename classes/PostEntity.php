<?php
class PostEntity
{
    protected $id;
    protected $title;
    protected $text;

    protected $date;
    protected $status;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->title = $data['title'];
        $this->text = $data['text'];
        $this->date = $data['date'];
        $this->status = $data['status'];
    }
    public function getId() {
        return $this->id;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getText() {
        return $this->text;
    }
    public function getDate() {
        return $this->date;
    }
    public function getStatus() {
        return $this->status;
    }
//    public function getShortDescription() {
//        return substr($this->description, 0, 20);
//    }

}