<?php
class PostMapper extends Mapper
{

    public function getPosts()
    {
        $sql = "SELECT * FROM posts ORDER BY datapost DESC";
        $stmt = $this->db->query($sql);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new PostEntity($row);
        }
        return $results;

    }

    public function getPostById($post_id) {
        $sql = "SELECT * FROM posts WHERE posts.id = :post_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["post_id" => $post_id]);
        if($result) {
            return new PostEntity($stmt->fetch());
        }
    }


    public function getLastPostsID()
    {
        $sql = "SELECT id FROM posts ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->query($sql);
        $results[] =$stmt->fetch();

        if (!isset($results[0])) {
            return null;
        }

        return $lastPostId = $results[0]['id'];

    }

    public function save(PostEntity $post)
    {
        $sql = "INSERT INTO posts (id, title, text, state, datapost) VALUES (:id, :title, :text, :state, :datapost)";
        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            "id" => $post->getId(),
            "title" => $post->getTitle(),
            "text" => $post->getText(),
            "state" => $post->getState(),
            "datapost" => $post->getDatapost(),
        ]);
    }

    public function update(PostEntity $post)
    {
        $sql = "UPDATE posts SET title = :title, text = :text, state = :state, datapost = :datapost  WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            "id" => $post->getId(),
            "title" => $post->getTitle(),
            "text" => $post->getText(),
            "state" => $post->getState(),
            "datapost" => $post->getDatapost(),
        ]);

    }
    public function delete(PostEntity $post)
    {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            "id" => $post->getId(),
        ]);
    }

}


