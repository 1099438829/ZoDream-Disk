<?php
namespace Infrastructure;

class Tree {
    public static function make(array $args, $id, $level = 0) {
        $data = [];
        foreach ($args as $item) {
            if ($item['parent_id'] != $id) {
                continue;
            }
            $item['level'] = $level;
            $item['children'] = self::make($args, $item['id'], $level + 1);
            $data[] = $item;
        }
        return $data;
    }

    public static function makeUl(array $data, $id = 0, \Closure $callback) {
        $html = null;
        foreach ($data as $item) {
            if ($item['parent_id'] != $id) {
                continue;
            }
            $html .= '<li><span>'.$item['id'].'</span><span>'.$item['name'].'</span>'.$callback($item).self::makeUl($data, $item['id'], $callback).'</li>';
        }
        if (empty($html)) {
            return null;
        }
        return "<ul>{$html}</ul>";
    }

    public static function makeFolder(array $data, $id = 0) {
        $html = null;
        foreach ($data as $item) {
            if ($item['parent_id'] != $id) {
                continue;
            }
            $html .= '<li><span>'.$item['name'].'</span>'.self::makeUl($data, $item['id']).'</li>';
        }
        if (empty($html)) {
            return null;
        }
        return "<ul>{$html}</ul>";
    }

    public static function makeOption(array $data, $selected = 0, $id = 0, $level = 0) {
        $html = null;
        foreach ($data as $item) {
            if ($item['parent_id'] != $id) {
                continue;
            }
            $html .= '<option value="'.$item['id'].'" '.
                ($selected == $item['id'] ? 'selected' : null).'> '.str_repeat('---', $level).' '.$item['name'].'</option>'.
                self::makeOption($data, $selected, $item['id'], $level + 1);
        }
        return $html;
    }
}