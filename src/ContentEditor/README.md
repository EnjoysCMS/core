```php

use EnjoysCMS\Core\Components\ContentEditor\EditorConfig;
use EnjoysCMS\Core\Components\ContentEditor\NullEditor;
use EnjoysCMS\Core\Components\ContentEditor\EditorFactory;
use EnjoysCMS\Core\Components\ContentEditor\ContentEditor;

class View {
    public function __construct(private ContentEditor $contentEditor) {}
    public function __invoke(array $configParams){
        return $this->contentEditor
            ->withConfig($configParams)
            ->setSelector('#description')
            ->getEmbedCode();
    }   
}

//$configParams = [
//    StubContentEditor::class => null
//]
//
//$configParams = [
//    StubContentEditor::class => 'template_simple.tpl'
//]
//
//$configParams = [
//    StubContentEditor::class => [
//        'template' => 'template_simple.tpl',
//        'other_parameters' => [/*...*/]
//    ]
//]


```
