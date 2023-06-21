<?php
// https://compojoom.com/documentation/ccomment?view=book&page=245:Settings.php
defined( '_JEXEC' ) or die( 'Restricted access' );
class ccommentComponentMAMSSettings extends ccommentComponentSettings
{
	/**
	 * categories option list used to display the include/exclude category list in setting
	 * must return an array of objects (id,title)
	 *
	 * @return array() - associative array (id, title)
	 */
	public function getCategories()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('sec_id as id, sec_name as title');
		$query->from('#__mams_secs');
		$query->where('published = 1');
		$query->order('sec_name ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

}