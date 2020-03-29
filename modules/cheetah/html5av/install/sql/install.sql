
-- alerts

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_video_player', '', '', 'ChWsbService::call(''h5av'', ''response_video_player'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_videos', 'display_player', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_video_convert', '', '', 'ChWsbService::call(''h5av'', ''response_video_convert'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_videos', 'convert', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_video_delete', '', '', 'ChWsbService::call(''h5av'', ''response_video_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_videos', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_video_embed', '', '', 'ChWsbService::call(''h5av'', ''response_video_embed'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_videos', 'embed_code', @iHandler);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_audio_player', '', '', 'ChWsbService::call(''h5av'', ''response_audio_player'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_sounds', 'display_player', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_audio_convert', '', '', 'ChWsbService::call(''h5av'', ''response_audio_convert'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_sounds', 'convert', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_audio_delete', '', '', 'ChWsbService::call(''h5av'', ''response_audio_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_sounds', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_audio_embed', '', '', 'ChWsbService::call(''h5av'', ''response_audio_embed'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_sounds', 'embed_code', @iHandler);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_cmts_player', '', '', 'ChWsbService::call(''h5av'', ''response_cmts_player'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_video_comments', 'embed', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_cmts_convert', '', '', 'ChWsbService::call(''h5av'', ''response_cmts_convert'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_video_comments', 'convert', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'ch_h5av_cmts_delete', '', '', 'ChWsbService::call(''h5av'', ''response_cmts_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'ch_video_comments', 'delete', @iHandler);
