<?php
class PostMapper extends Mapper
{

    public function getPosts()
    {
        $sql = "SELECT * from post ORDER by datapost DESC";
        $stmt = $this->db->query($sql);

        $results = [];

        while ($row = $stmt->fetch()) {
            $results[] = new PostEntity($row);
        }

        return $results;

    }


    //pronto
    public function getPostById($id)
    {
        $sql = "SELECT * from post WHERE id == $id ";
        $stmt = $this->db->query($sql);

        $results = [];

        while ($row = $stmt->fetch()) {
            $results[] = new PostEntity($row);
        }

        return $results;

    }

    public function getLastPostsID()
    {
        $sql = "SELECT id from post ORDER by id DESC LIMIT 1";
        $stmt = $this->db->query($sql);

        $results[] =$stmt->fetch();

        $lastPostId = $results[0]['id'];

        return $lastPostId;

    }

    public function save(PostEntity $post) {
        $sql = "insert into post (id, title, text, state, datapost) values (:id, :title, :text, :state, :datapost)";
        $stmt = $this->db->prepare($sql);


        $result = $stmt->execute([
            "id" => $post->getId(),
            "title" => $post->getTitle(),
            "text" => $post->getText(),
            "state" => $post->getState(),
            "datapost" => $post->getDatapost(),
        ]);


//        $sql = "insert into tickets
//            (title, description, component_id) values
//            (:title, :description,
//            (select id from components where component = :component))";
//        $stmt = $this->db->prepare($sql);
//        $result = $stmt->execute([
//            "title" => $ticket->getTitle(),
//            "description" => $ticket->getDescription(),
//            "component" => $ticket->getComponent(),
//        ]);
//        if(!$result) {
//            throw new Exception("could not save record");
//        }


    }
}


