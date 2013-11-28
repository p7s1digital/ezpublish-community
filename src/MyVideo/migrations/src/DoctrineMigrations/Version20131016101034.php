<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20131016101034 extends eZPublishPackageMigration
{

    private $textTeaser = array(
        'content_class_id' => 'myvideo_text_teaser',
        'file' => 'myvideo_text_teaser-1.1-1.ezpkg',
        'description' => 'MyVideo Text Teaser',
    );

    // sushibar-layout objects in sushibar-layout node
    protected $layouts = array(

        array(
            'parent_node_id' => 'myvideo-folder-sushibar-layouts',
            'class_identifier' => 'myvideo_sushibar_layout',
            'remote_id' => 'myvideo-sushibar-layout-eight-small-single-row',
            'attributes' => array(
                'title' => 'Eight Small Single Row',
                'identifier' => 'eight-small-single-row',
                'description' => 'Eine Reihe mit acht Elementen.',
                'image' => array(
                    'src' => 'preview_one-big-four-small.png',
                    'title' => '"Produkt kann von dieser Darstellung in Farbe und Form abweichen ;)"',
                ),
            )
        ),

        array(
            'parent_node_id' => 'myvideo-folder-sushibar-layouts',
            'class_identifier' => 'myvideo_sushibar_layout',
            'remote_id' => 'myvideo-sushibar-layout-four-small-text-outside',
            'attributes' => array(
                'title' => 'Four Small Text Outside',
                'identifier' => 'four-small-text-outside',
                'description' => 'Vier kleine Bilder mit Titel unterhalb.',
                'image' => array(
                    'src' => 'preview_one-big-four-small.png',
                    'title' => '"Produkt kann von dieser Darstellung in Farbe und Form abweichen ;)"',
                ),
            )
        ),

        array(
            'parent_node_id' => 'myvideo-folder-sushibar-layouts',
            'class_identifier' => 'myvideo_sushibar_layout',
            'remote_id' => 'myvideo-sushibar-layout-one-big-text-two-small',
            'attributes' => array(
                'title' => 'One Big Text Two Small',
                'identifier' => 'one-big-text-two-small',
                'description' => 'Ein großer Bild-, dann ein großer Text-Teaser, dann zwei weitere kleine Teaser.',
                'image' => array(
                    'src' => 'preview_one-big-four-small.png',
                    'title' => '"Produkt kann von dieser Darstellung in Farbe und Form abweichen ;)"',
                ),
            )
        ),
    );

    public function up(Schema $schema)
    {
        $this->installContentClass($this->textTeaser);

        $this->initScripting();
        $this->createTreeNodes($this->layouts);
        $this->shutdownScripting();

    }

    public function down(Schema $schema)
    {
        $this->removeContentClass($this->textTeaser);

        $this->initScripting();
        $this->removeTreeNodes($this->layouts);
        $this->shutdownScripting();

    }
}
