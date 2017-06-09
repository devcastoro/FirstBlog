<?php
class PostEntity
{
    protected $id;
    protected $title;
    protected $text;
    protected $datapost;
    protected $state;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data)
    {
        // no id if we're creating
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        $this->title = $data['title'];
        $this->text = $data['text'];
        $this->datapost = $data['datapost'];
        $this->state = $data['state'];
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
    public function getDatapost() {
        return $this->datapost;
    }
    public function getState() {
        return $this->state;
    }
}