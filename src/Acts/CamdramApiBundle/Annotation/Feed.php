<?php
namespace Acts\CamdramApiBundle\Annotation;


/**
* @Annotation
*/
class Feed {

    private $name;

    private $description;

    private $template;

    private $titleField = 'title';

    private $createdAtField = 'created_at';

    private $updatedAtField = 'updated_at';

    public function __construct(array $options)
    {
        $this->name = $options['name'];
        $this->description = $options['description'];
        $this->template = $options['template'];

        if (isset($options['titleField'])) $this->titleField = $options['titleField'];
        if (isset($options['createdAtField'])) $options['createdAtField'];
        if (isset($options['updatedAtField'])) $options['updatedAtField'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getTitleField()
    {
        return $this->titleField;
    }

    public function getCreatedAtField()
    {
        return $this->createdAtField;
    }

    public function getUpdatedAtField()
    {
        return $this->updatedAtField;
    }

} 