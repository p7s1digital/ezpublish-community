<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131111105325 extends eZPublishPackageMigration
{
    // sushibar-layout objects in sushibar-layout node
    protected $layouts = array(
        array(
            'parent_node_id' => 'myvideo-folder-sushibar-layouts',
            'class_identifier' => 'myvideo_sushibar_layout',
            'remote_id' => 'myvideo-sushibar-layout-pure-text',
            'attributes' => array(
                'title' => 'Pure Text Single Row',
                'identifier' => 'pure-text',
                'description' => 'Eine reine text Sushibar',
                'image' => array(
                    'src' => 'preview_pure-text.png',
                    'title' => '"Produkt kann von dieser Darstellung in Farbe und Form abweichen ;)"',
                ),
            )
        ),

        array(
            'parent_node_id' => 'myvideo-folder-sushibar-layouts',
            'class_identifier' => 'myvideo_sushibar_layout',
            'remote_id' => 'myvideo-sushibar-layout-bigtext-two-small',
            'attributes' => array(
                'title' => 'Big Text two small',
                'identifier' => 'bigtext-two-small',
                'description' => 'Eine Text Sushibar mit zwei Bildern',
                'image' => array(
                    'src' => 'preview_bigtext-two-small.png',
                    'title' => '"Produkt kann von dieser Darstellung in Farbe und Form abweichen ;)"',
                ),
            )
        )
    );

    public function up(Schema $schema)
    {
        $this->initScripting();
        $this->createTreeNodes($this->layouts);
        $this->shutdownScripting();
    }

    public function down(Schema $schema)
    {
        $this->initScripting();
        $this->removeTreeNodes($this->layouts);
        $this->shutdownScripting();
    }
}
