<?
	class CComments {
		public function submit() {
			$_REQUEST[comment] = strip_tags($_REQUEST[comment]);
			if (strlen($_REQUEST[comment])<5) return false;
			$_REQUEST[name] = str_replace(array('"',"'"),'',$_REQUEST[name]);
			if (trim($_REQUEST[name])=='') $_REQUEST[name] = self::generateNick();
			CMysql::insert('comments', array(
				'parent_id' => 0,
				'item_id' => $_REQUEST[item_id],
				'name' => $_REQUEST[name],
				'email' => $_REQUEST[email],
				'comment' => $_REQUEST[comment],				
				'timestamp' => time(),
				'ip' => $_SERVER[REMOTE_ADDR],
			)) or die(mysql_error());
			mailNotification('Добавлен комментарий',$_REQUEST[comment]);
			return true;
		}
		
		public function generateNick() {
			$words = array(
				0 => array(
					'm' => array('пушистый', 'игривый', 'хорошенький', 'мохнатый', 'полосатый', 'важный', 'знойный', 'веселый', 'пакостный', 'четкий', 'смешной', 'веселый', 'приветливый', 'ворчливый', 'интеллигентный', 'везучий', 'понятливый', 'огнеопасный', 'брутальный', 'хмельной', 'сексуальный', 'сообразительный', 'невообразимый', 'непокорный', 'эффектный', 'прикольный', 'озорной', 'неуловимый', 'маститый', 'креативный', 'глазастый'),
					'f' => array('хорошенькая', 'обаятельная', 'очаровательная', 'привлекательная', 'прелестная', 'чудесная', 'неотразимая', 'элегантная', 'утонченная', 'изящная', 'яркая', 'эффектная', 'шикарная', 'безупречная', 'совершенная', 'бесподобная', 'сногсшибательная', 'несравненная', 'весёлая', 'темпераментная', 'грациозная', 'волшебная', 'сказочная', 'непостижимая', 'невероятная', 'загадочная', 'таинственная', 'интересная', 'пленительная', 'незабываемая', 'стильная', 'жаркая', 'сладкая', 'мелодичная', 'великолепная', 'любопытная', 'знойная', 'страстная', 'игривая', 'полосатая', 'пушистая', 'мокрая'),
				),
				1 => array(
					'm' => array('Муравей', 'бабуин', 'Барсук', 'удав', 'верблюд', 'кот', 'хамелеон', 'таракан', 'краб', 'крокодил', 'олень', 'дельфин', 'орел', 'слон', 'сокол', 'хорек', 'жираф', 'бегемот', 'конь', 'лев', 'омар', 'аист', 'осьминог', 'страус', 'попугай', 'павлин', 'пеликан', 'пингвин', 'кабан', 'утконос', 'богомол', 'зайчик', 'тигр', 'енот', 'носорог', 'скорпион', 'паук', 'кит', 'волк', 'медведь', 'лось', 'горностай', 'соболь', 'леопард', 'ягуар', 'барс'),
					'f' => array('обезьянка', 'мышка', 'бабочка', 'кошка', 'сороконожка', 'собачка', 'рыбка', 'лисичка', 'лягушка', 'чайка', 'лошадка', 'медузка', 'норка', 'сова', 'свинка', 'зайка', 'барашка', 'белочка', 'тигра', 'черепашка', 'пчелка', 'ласка', 'пантерка', 'синичка', 'ласточка', 'голубка', 'рысь', 'пантерка', 'панда', 'фосса', 'рысь', 'пума'),
				),
			);
			$gender = array('m', 'f');
			$cur_gender = $gender[rand(0,1)]; 

			$nick = $words[0][$cur_gender][rand(0,count($words[0][$cur_gender])-1)].' '.$words[1][$cur_gender][rand(0,count($words[1][$cur_gender])-1)];
			return mb_convert_case($nick, MB_CASE_TITLE, "UTF-8"); 
		}
		
		public function stringToNumber($str, $max) {
			$str = md5($str);
			$num = base_convert(substr($str,0,2), 16, 10);
			$num = floor($num * $max/256) + 1;
			return $num;
		}
	}
?>