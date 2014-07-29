<?
	$id = (int)$_GET[id];
	$category = (int)$_GET[category];
	$category_base = (int)$_GET[category_base];
	
	if ($id==0) {
		$tpl[content] .= "<div class='itemsList'>";
		$res = $db->query("select items.*, categories.parent_id from items left join categories on items.category_id=categories.id where items.flag=1 "
			.($category>0 ? "and items.category_id='$category'" : '')
			.($category_base>0 ? "and categories.parent_id='$category_base'" : '')
			. " order by items.id desc"
			.($category==0 && $category_base==0 ? " limit 18" : '')
		) or die(mysql_error());
		while ($row=$db->fetch($res)) {
			$tpl[content] .= "
				<a href='".format_url('item',$row)."' class='item'>
					<div class='header'><span>$row[name]</span></div>
					<div class='image'><img src='/img.php?file=upload/items/$row[id]_1.jpg&width=230&fixed_asp=".(230/178)."' width=230 height=178 /></div>
					<div class='footer'>
						<div class='left'>$row[price] грн</div>
						<div class='right'>Подробнее</div>
					</div>
				</a>
				";
		}
		$tpl[content] .= "</div>";
	}
	
	else {
		if (isset($_REQUEST[submit_comment])) {
			$res = CComments::submit();
			$uri = explode('?',$_SERVER[REQUEST_URI]);
			header("location: ".$uri[0]."?comment_result=".$res, true);
		}
		if (isset($_REQUEST[comment_result])) {
			if ($_REQUEST[comment_result]==1) $tpl[sys_message] = "Спасибо, ваш комментарий будет опубликован после проверки.";
			else $tpl[sys_message] = "<!--error-->Комментарий слишком короткий";
		}
		
		$row = $db->get_row("select * from items where id='$id'");
		
		$tpl[item] = $row;
		$tpl[add_button] = ($cbasket->getItem($row[id])!==false ? "<a class='add' href='/cart'>Товар в корзине</a>" : "<a class='add_to_basket add' data-id='$row[id]'><i class='fa fa-shopping-cart fa-2x'></i> Добавить в корзину</a>");
		$tpl[img_previews] .= "<a href='/big_image/$row[id]/1/".str_replace(' ','-',translit($row[name])).".jpg' class='bigImage' rel='gallery'><img src='/img.php?file=upload/items/$row[id]_1.jpg&width=360'/></a>";
		for ($i=2; $i<=20; $i++) if (file_exists("upload/items/$row[id]_$i.jpg")) $tpl[img_previews] .= "<a href='/big_image/$row[id]/$i.jpg' class='smallImage' rel='gallery'><img src='/square_thumb/$row[id]/$i.jpg' width=100 height=100 /></a>";
		$tpl[title] = "$row[name]";
		$tpl[description] = $row[description];
		$row[variants] = explode("\n",trim($row[variants]));
		if (count($row[variants])>1) {
			foreach ($row[variants] as $v) {
				$tpl[variants] .= "<option>$v</option>";
			}
			$tpl[variants] = "<h4>Вариант</h4> <select name='variant' id='variant'>$tpl[variants]</select>";
		}
		
		// comments
		$tpl[comments] = '';
		$res_comment = $db->query("select * from comments where item_id='$row[id]' and flag=1");
		while ($row_comment=$db->fetch($res_comment)) {
			$row_comment[comment] = str_replace("\n", '<br>', $row_comment[comment]);
			$tpl[comments] .= "<div class='comment'>
				<div class='header'>$row_comment[name] <div class='date'>".date('d.m.Y',$row_comment[timestamp])."</div></div>
				<div class='content'>$row_comment[comment]</div>
				</div>";
		}
		
		$tpl[content] = get_tpl("items_details.tpl");

	}
	
	$tpl[menu_list] = $db->query("select name, id from categories where parent_id=0 and flag=1",true);
	if ($_SERVER[REQUEST_URI]=='/') {
		$tpl[header_add] = get_tpl("header_slider.tpl");		
	}
	//else 
	//$tpl[left_menu] = get_tpl("left_menu.tpl");

?>