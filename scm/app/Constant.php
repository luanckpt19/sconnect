<?php

namespace App;

class Constant {
	public const HOME = '/home';
	public const COOKIE_SCM_TOKEN 	= 'scm_tk';

	public const BITLY_TOKEN = '92a2d4db0dbc821d82e07976abb8fc0dd48b9fb9';

	/* */
	public const PERMISSION = [1=>'Administrator', 2=>'Editor', 3=>'Reporter'];
	/* */
	public const COMMENT_TYPE = [1=>'Tích cực', 2=>'Trung lập', 3=>'Tiêu cực'];
	public const MIME_TYPE_VIDEO = 'video';
	public const MIME_TYPE_AUDIO = 'audio';
	public const MIME_TYPE_IMAGE = 'image';
	public const MIME_TYPES = ['audio'=>'Audio', 'video'=>'Video', 'image'=>'Image'];
	public const MIME_ICONS = ['audio'=>'far fa-file-audio', 'video'=>'far fa-file-video', 'image'=>'far fa-image'];
	public const GENDERS = ['Tất cả', 'Nam', 'Nữ'];
	public const TICKET_STATUS = ['Nháp', 'Đã gửi MKT', 'MKT đang duyệt', 'Đang thảo luận', 'Đang chạy', 'Tạm dừng', 'Đã kết thúc'];
	public const KINDS = ['Chạy ND mới', 'ND cũ chạy lại'];
	public const STAFF_GROUPS = ['-'=>'', 'product'=>'Sản xuất', 'qtk'=>'Quản trị kênh', 'mkt'=>'Marketing'];
	public const PRODUCT = 'product';
	public const QTK = 'qtk';
	public const MKT = 'mkt';
}
