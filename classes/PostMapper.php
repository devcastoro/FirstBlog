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

    /**
     * Get one ticket by its ID
     *
     * @param int $post_id The ID of the post
     * @return PostEntity The post
     */
    public function getPostById($post_id) {
        $sql = "SELECT * from post where post.id = :post_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["post_id" => $post_id]);
        if($result) {
            return new PostEntity($stmt->fetch());
        }
    }


    public function getLastPostsID()
    {
        $sql = "SELECT id from post ORDER by id DESC LIMIT 1";
        $stmt = $this->db->query($sql);

        $results[] =$stmt->fetch();

        $lastPostId = $results[0]['id'];

        return $lastPostId;

    }

    public function save(PostEntity $post)
    {
        $sql = "insert into post (id, title, text, state, datapost) values (:id, :title, :text, :state, :datapost)";
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
        $sql = "UPDATE post SET title = :title, text = :text, state = :state, datapost = :datapost  WHERE id = :id";

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
        $sql = "DELETE FROM post WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            "id" => $post->getId(),
        ]);
    }

}


