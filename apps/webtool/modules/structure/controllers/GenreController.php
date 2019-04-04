<?php

class GenreController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = \Manager::getSession()->idLanguage;
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->render();
    }

    public function modelTree()
    {
        $structure = Manager::getAppService('structuregenre');
        if ($this->data->id == '') {
            $children = $structure->listAll($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Genre Types',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 't') {
            $json = $structure->listGenreByGenreType(substr($this->data->id, 1), $this->idLanguage);
        }
        $this->renderJson($json);
    }

    public function formNewGenreType()
    {
        $nodeId = $this->data->id;
        if ($nodeId{0} == 'g') {
            $this->data->id = substr($this->data->id, 1);
        }
        $this->data->save = "@structure/genre/newGenreType|formNewGenreType";
        $this->data->close = "!$('#formNewGenreType_dialog').dialog('close');";
        $this->data->title = _M('new GenreType');
        $this->render();
    }

    public function newGenreType()
    {
        try {
            $model = new fnbr\models\GenreType();
            $this->data->genretype->entry = 'gty_' . str_replace('gty_', '', strtolower($this->data->genretype->entry));
            $model->save($this->data->genretype);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->genretype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formUpdateGenreType()
    {
        $model = new fnbr\models\GenreType($this->data->id);
        $this->data->genretype = $model->getData();
        $this->data->genretype->entry = str_replace('gty_', '', strtolower($this->data->genretype->entry));
        $this->data->save = "@structure/genre/updateGenreType|formUpdateGenreType";
        $this->data->close = "!$('#formUpdateGenreType_dialog').dialog('close');";
        $this->data->title = 'GenreType: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function updateGenreType()
    {
        try {
            $model = new fnbr\models\GenreType($this->data->genreType->idGenreType);
            $this->data->genretype->entry = 'gty_' . str_replace('gty_', '', strtolower($this->data->genretype->entry));
            $model->updateEntry($this->data->genreType->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->genretype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


    public function formNewGenre()
    {
        $model = new fnbr\models\GenreType($this->data->id);
        $this->data->genreType = $model->getName();
        $this->data->save = "@structure/genre/newGenre|formNewGenre";
        $this->data->close = "!$('#formNewGenre_dialog').dialog('close');";
        $this->data->title = _M('new Genre');
        $this->render();
    }

    public function newGenre()
    {
        try {
            $model = new fnbr\models\Genre();
            $this->data->genre->entry = 'gen_' . str_replace('gen_', '', strtolower($this->data->genre->entry));
            $model->save($this->data->genre);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->genre->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formUpdateGenre()
    {
        $model = new fnbr\models\Genre($this->data->id);
        $this->data->genre = $model->getData();
        $this->data->genre->entry = str_replace('gen_', '', strtolower($this->data->genre->entry));
        $this->data->save = "@structure/genre/updateGenre|formUpdateGenre";
        $this->data->close = "!$('#formUpdateGenre_dialog').dialog('close');";
        $this->data->title = 'Genre: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function updateGenre()
    {
        try {
            $model = new fnbr\models\Genre($this->data->genre->idGenre);
            $this->data->genre->entry = 'gen_' . str_replace('gen_', '', strtolower($this->data->genre->entry));
            $model->updateEntry($this->data->genre->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->genre->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formDeleteGenre()
    {
        $ok = "^structure/genre/deleteGenre/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Warning: Genre will be deleted! Continue?', $ok);
    }

    public function deleteGenree()
    {
        try {
            $model = new fnbr\models\Genre($this->data->id);
            $model->delete();
            $this->renderPrompt('information', 'Genre deleted.', "!structure.reloadParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
