<?php /* ���������� �������� ��� ���������� ���

�����������: |

� ������ ��������� �� ��������� ������������� ���������� (���� �� ��������� - �� ����� �������).

���� ���������� ������ � ������ - ��������� ��������� �������� �� ����������� |, ���� �� � ������ - ��������� (���� ������, ����� �� ��������� ��� � �����������).

��������� � ���������� ����� ���� ������������ ������� ����, ����� ������ ����� ������� l,o,r ��� u, ��� ��������, ��� ������ ��� ��������:
u: - unic, ���������� ����� ���������� � ���� (����� ���� 1234)
l: - login, ������������������ ����������� �����
o: - openid, ������ ���������� (��������, dr-livsey.livejournal.com)
r: - realname, �������� ��� ���������� (��� �� ���������� ������� � ����������)
c: - ��� ������ �� CloudFlare (RU, US)
���� ������������ � ������-�� ����� �� ������, ������������ ��������� ���������. ���� �� ���������� ������, �� ����� �������������� ��� unic, �� ����� - ��� realname.
���� � ������������ ����������� 'podzamok' - �� ��� �����������.

{_DOSTUP: r:semenyaka,AMMO,������� ���������,o:dr-piliulkin.livejournal.com,u:1,345,17 | ������� � ������� �� ��� � 16, �������! | � ���� ���� �� �� ��������, ����� :((( _}

{_DOSTUP: Olegusha | | ������� ����� ��� Olegusha: ����� � ���������, ����� ����� ��� ���������� �� ��, �� ��� "������� �� �����", ����������� �� ��������, �������� ���������! _}

{_DOSTUP: Vasya_PU | ����, �� ���� ���� ����� �����, ������� ��� ������!!!! _}

*/

function DOSTUP($e) { list($spisok,$yes,$no)=explode('|',$e); $p=explode(',',$spisok);

	$GLOBALS['IS']['unic']=$GLOBALS['unic'];
        $GLOBALS['IS']['country']=$_SERVER['HTTP_CF_IPCOUNTRY'];

	$id=''; foreach($p as $l) { $l=c($l);

		if($l=='podzamok') { if($GLOBALS['podzamok']) return c($yes); continue; }

		if(substr($l,1,1)==':') {
			$c=substr($l,0,1);
			if($c=='u') { $id='unic'; }
			elseif($c=='l') { $id='login'; }
			elseif($c=='o') { $id='openid'; }
			elseif($c=='r') { $id='realname'; }
                        elseif($c=='c') { $id='country'; }
			if($GLOBALS['IS'][$id]==substr($l,2)) return c($yes);
			continue;
		}

		if($id=='') {
			if(intval($l)) { if($GLOBALS['IS']['unic']==$l) return c($yes); }
			else { if($GLOBALS['IS']['realname']==$l) return c($yes); }
			continue;
		}

		if($GLOBALS['IS'][$id]==$l) { return c($yes); }

	}

	return c($no);
}

?>