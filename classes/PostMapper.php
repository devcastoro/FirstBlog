<?php
class PostMapper extends Mapper
{

//TEST CODICE STILE CORRETTO, ma non funzionante

/*    public function getPosts()
    {

//        $sql = "SELECT t.id, t.title, t.description, c.component
//            from tickets t
//            join components c on (c.id = t.component_id)";

        $sql = "SELECT id, title, text, data, state  FROM post ORDER by id DESC";
        $stmt = $this->db->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
           $results[] = new PostEntity($row);

        }
        return $results;

    }
    */


//TEST FUNZIONANTE Stile non corretto
    public function getPosts()
    {

        $link = @mysqli_connect("127.0.0.1", "root", "", "firstblog");

        if (mysqli_connect_errno()) {
            echo "Connessione fallita: " . die (mysqli_connect_error());
        }


        // esecuzione della query
        $query = "SELECT text FROM post ORDER by id DESC";
        $result = @mysqli_query($link, $query);


        // controllo sul numero dei record coinvolti
        if (@mysqli_num_rows($result) != 0) {
            // risultato sotto forma di array numerico
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                echo $row[0] . "<br>";
            }

//            // risultato sotto forma di array asscociativo
//            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
//                echo $row['text'] . "<br>";
//            }
//
//            // risultato sotto forma di array numerico o associativo
//            while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
//                echo $row['id'] . "<br>";
//                echo $row[0] . "<br>";
//            }
        }

        // liberazione della memoria dal risultato della query
        @mysqli_free_result($result);

        // chiusura della connessione
        @mysqli_close($link);

    }
}


