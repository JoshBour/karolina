<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class JoinSelect extends AbstractHelper
{
    private $displayMethod;

    private $identifierMethod;

    private $joinMethod;

    private $list;

    private $selected;

    private $columns;

    /**
     * Returns a select element based on the given parameters.
     *
     * @param array $options The config options
     * @return string
     */
    public function __invoke(array $options)
    {
        // $list, $columns, $joinMethod, $identifierMethod, $displayMethod, $selected
        $this->list = $options["list"];
        $this->selected = $options["selected"];
        $this->identifierMethod = $options["identifierMethod"];
        $this->joinMethod = $options["joinMethod"];
        $this->displayMethod = $options["displayMethod"];
        $this->columns = $options["columns"];

        $rendered = sprintf('<div class="joinSelect">
<select class="joinList">%s</select>
<div class="joinTableWrapper">%s</div>
<div class="addJoin">%s</div>
</div>', $this->renderOptions(), $this->renderAttributeTable(), $this->renderAddButton());

        return $rendered;
    }


    private function renderOptions($searchAll = false, $option = null)
    {
        $options = "";
        foreach ($this->list as $join) {
            $joinId = $join->{$this->identifierMethod}();
            $selected = $searchAll ? ($this->isSelected($joinId) ? " selected" : "") : $option ? ($option == $joinId ? " selected" : "") : "";
            $options .= sprintf('<option value="%s"%s>%s</option>',
                $joinId,
                $selected,
                $join->{$this->displayMethod}());
        }
        return $options;
    }

    private function renderAttributeTable()
    {
        $tableHead = sprintf("<tr>%s</tr>", $this->renderColumns());

        return sprintf('<table class="activeJoins"><thead>%s</thead><tbody>%s</tbody></table>', $tableHead, $this->renderTableBody());

    }

    private function renderAddButton()
    {
        return '<span class="button">+ Add New</span>';
    }

    private function renderTableBody()
    {

        $result = "";
        if (!empty($this->selected)) {
            foreach ($this->selected as $join) {
                $result .= sprintf("<tr>%s</tr>", $this->renderJoinValues($join));
            }
        }
        return $result;
    }

    private function renderJoinValues($join)
    {
        $columns = array_slice($this->columns,1);
        $joinId = is_array($join) ? $join['joinId'] : $join->{$this->joinMethod}()->{$this->identifierMethod}();
        $result = '<td><select class="joinList" style="display:inline-block">' . $this->renderOptions(false, $joinId) . '</select>';
        foreach ($columns as $column) {
            $value = is_array($join) ? $join[$column] : $join->{'get' . ucwords($column)}();
            $result .= '<td><input type="text" name="' . $column . '" value="' . $value . '" /></td>';
        }
        $result .= '<td><span class="joinDelete button">Delete</span></td>';
        return $result;
    }

    private function renderColumns()
    {
        $columns = "";
        foreach ($this->columns as $column) {
            $columns .= "<th>" . ucfirst($column) . "</th>";
        }
        $columns .= "<th>Delete</th>";
        return $columns;
    }

    private function isSelected($joinId)
    {
        foreach ($this->list as $join) {
            if ($join->{$this->joinMethod}()->{$this->identifierMethod}() == $joinId)
                return true;
        }
        return false;
    }
}