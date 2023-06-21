<?php
// https://compojoom.com/documentation/ccomment?view=book&page=246:Nameofcomponent.php%20(content.php,%20k2.php,%20docman.php%20etc)
defined( '_JEXEC' ) or die( 'Restricted access' );

class ccommentComponentMAMSPlugin extends ccommentComponentPlugin
{

	/**
	 * With this function we determine if the comment system should be executed for this
	 * content Item
	 * @return bool
	 */
	public function isEnabled() {
		$config = ccommentConfig::getConfig('com_mams');
		$row = $this->row;

		$secs = $config->get('basic.categories', array());
		$include = $config->get('basic.include_categories', 0);

		$result = in_array((($row->sec == 0) ? -1 : $row->sec), $secs);

		if ($include && $result)
		{
			return true; /* include and selected */
		}

		if (!$include && $result)
		{
			return false; /* exclude and selected */
		}

		if (!$include)
		{
			return true; /* was not excluded */
		}

		return false;
	}

	/**
	 * This function decides whether to show the comments
	 * in an article/item or to show the readmore link
	 *
	 * If it returns true - the comments are shown
	 * If it returns false - the showReadon function will be called
	 * @param int - the content/item id
	 * @return boolean
	 */
	public function isSingleView() {
		return true;
	}

	/**
	 * This function determines whether to show the comment count or not
	 * @return bool
	 */
	public function showReadOn() {
		return true;
	}

	/**
	 * @param int  $contentId
	 * @param int  $commentId
	 *
	 * @param bool $xhtml
	 *
	 * @return string - the URL to the comment/item
	 */
	public function getLink($contentId, $commentId = 0, $xhtml = true) {
		$url = JRoute::_('index.php?option=com_mams&view=article&artid='.$contentId);

		if ($commentId)
		{
			$url .= "#!/ccomment-comment=$commentId";
		}

		return $url;
	}

	/**
	 * @param $ids - the ids of the items that we look for title
	 *
	 * @return mixed - array with objects (id, title) sorted by id
	 */
	public function getItemTitles($ids) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('art_id as id,art_title as title')->from('#__mams_articles')
		      ->where('art_id IN (' . implode(',', $ids) . ')');

		$db->setQuery($query);

		return $db->loadObjectList('id');
	}

	/**
	 * Returns the id of the author of an item
	 *
	 * @param int $contentId
	 *
	 * @return mixed
	 */
	public function getAuthorId($contentId) {
		return false;
	}
}