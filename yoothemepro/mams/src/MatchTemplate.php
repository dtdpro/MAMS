<?php


use Joomla\CMS\Document\Document;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

class MatchTemplate
{
    public Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle($view, $tpl): ?array
    {
        if ($tpl) {
            return null;
        }

        $layout = $view->getLayout();
        $context = $view->get('context');

        if ($context === 'com_mams.artlist' && $layout === 'section') {
            $section = $view->secinfo[0];
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'secid' => $section->sec_id
                ],
                'params' => [
                    'section' => $section,
                    'items' => $view->get('items'),
                    'pagination' => $pagination,
                    'session' => JFactory::getSession()
                ],
            ];
        }

        if ($context === 'com_mams.artlist' && $layout === 'category') {
            $category = $view->catinfo[0];
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'catid' => $category->cat_id
                ],
                'params' => [
                    'category' => $category,
                    'items' => $view->get('items'),
                    'pagination' => $pagination,
                    'session' => JFactory::getSession()
                ],
            ];
        }


        if ($context === 'com_mams.artlist' && $layout === 'catsec') {
            $section = $view->secinfo[0];
            $category = $view->catinfo[0];
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'catid' => $category->cat_id,
                    'secid' => $section->sec_id
                ],
                'params' => [
                    'section' => $section,
                    'category' => $category,
                    'items' => $view->get('items'),
                    'pagination' => $pagination,
                    'session' => JFactory::getSession()
                ],
            ];
        }


        return null;
    }
}
