INSERT INTO `sys_box_download` (`title`, `url`, `onclick`, `desc`, `icon`, `order`, `disabled`) VALUES
('_ch_desktop_title', "php:return ChWsbService::call('desktop', 'get_file_url');", '', '_ch_desktop_desc', 'desktop', 1, 0);
