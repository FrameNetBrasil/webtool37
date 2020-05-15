## List of deliverables vs Schedule

### Data Import/Export Pipeline

While all the user have to do is import a video file, what happens under the hood is mildly more complex:

#### The workflow

_*@PRISHIta123 Take some time to expand/make changes to this workflow*_

1. User import a video file (via direct upload or importing from a url);
2. File/URL is validated (checking that the URL points to a video file);
3. Check the database for duplicates (inform user/drop duplicate file);
4. Check video width/height to make sure they meet a minimum constraint;
5. Non-duplicate video is uploaded/scraped and stored;
6. Audio track extracted and converted (FLAC / 44,100 Hz / Mono) and stored;
7. Video converted (MP4/H.264) and stored;
8. Video thumbnails generated and stored;
9. Audio file uploaded to Cloud Storage/Speech API;
10. Transcription returns from Cloud Speech API and stored;
11. Subtitles extracted from video with Python-tesseract and stored;
12. Transcription and subtitles synced/aligned and merged into a single text file;
13. User review the video and sentences (side by side) for validation;
14. Transcriptions and subtitles are organized into sentences and stored according to the Webtool standard;
15. Reviewed file is uploaded to the FrameNet Webtool.

### Pipeline Architecture



### Vídeo Import/Convert

### Importing from URL

After a video URL has been entered by the user, it must be sent to the pipeline which processes it through several components that are executed sequentially.

Each pipeline component is a Python class that implements a simple method. They receive an item and perform an action over it (also deciding if the item should continue through the pipeline or be dropped and no longer processed).

Typical actions include:

• validating data (checking that the URL points to a video file)
• checking for duplicates (and dropping them)
• storing the video in the database

The pipeline needs a few extra functions for processing video files:

• check video width/height to make sure they meet a minimum constraint (720x480/WideSD)
• convert all downloaded videos to a common format (MP4) and codec (H.264)
• thumbnail generation
• keep an internal queue of those media URLs which are currently being scheduled for download, and connect those responses that arrive containing the same media to that queue (this avoids downloading the same media more than once)

### Filtering out small videos

When using the video pipeline, users might try to upload videos which are too small. The tool should restrict videos which do not have the minimum allowed size in the VIDEO_MIN_HEIGHT and VIDEO_MIN_WIDTH settings.

For example:

    VIDEO_MIN_HEIGHT = 480
    VIDEO_MIN_WIDTH = 720

It should be possible to set just one size constraint or both. When setting both of them, only videos that satisfy both minimum sizes will be saved. For the above example, videos of sizes (640 x 480) or (800 x 460) will all be dropped because at least one dimension is shorter than the constraint.

By default, there are no size constraints, so all videos are processed.

### File storage system

The video files should be stored using a SHA1 hash of their URLs for the file names.

For example, the following video URL:

    https://youtu.be/j1IfooX0_Hw

Whose SHA1 hash is:

    abcf9fa8e0d025ed1a35e425122a4de86980334b

Will be stored in the following file:

    <VIDEOS_STORE>/full/abcf9fa8e0d025ed1a35e425122a4de86980334b.mp4

Where:

`<VIDEOS_STORE>` is the directory defined in `VIDEO_STORE` setting for the video pipeline.

`/full` is a sub-directory to separate full videos from thumbnails (if used) and video segments.

### Thumbnail generation for videos

The video pipeline should automatically create thumbnails of the downloaded videos.

In order to use this feature, users will set `IMAGES_THUMBS` to a dictionary where the keys are the thumbnail names and the values are their dimensions.

For example:

    IMAGES_THUMBS = {
        'small': (240, 180),
        'big': (320, 240),
    }

When you use this feature, the video pipeline will create thumbnails of the each specified size with this format:

    <IMAGES_STORE>/thumbs/<size_name>/<video_id>.jpg

Where:

`<size_name>` is the one specified in the `IMAGES_THUMBS` dictionary keys (small/big)
`<video_id>` is the SHA1 hash of the image url

Example of image files stored using small and big thumbnail names:

    <IMAGES_STORE>/thumbs/small/abcf9fa8e0d025ed1a35e425122a4de86980334b.jpg
    <IMAGES_STORE>/thumbs/big/abcf9fa8e0d025ed1a35e425122a4de86980334b.jpg
    
### Audio Transcription

Transcriptions should have timestamps that identify the exact point in an audio/video where the given text was spoken.

    For example: 00:00:08,204 –> 00:00:10,143 - Good morning.

Timestamps will use the format `[HH:MM:SSS`] where `HH`, `MM`, `SSS` are hours, minutes and milliseconds from the beginning and ending of each sentence in the audio track.

Once finished processing, the Speech-to-Text API will return the transcription to be stored in the following file:

    <TEXT_STORE>/transcripts/<video_id>.txt

###### /* [Via IBM Cloud Speech-to-text?](https://cloud.ibm.com/catalog/services/speech-to-text)

### Subtitle Extraction

*Python-tesseract is an optical character recognition (OCR) tool for python. That is, it will recognize and “read” the text embedded in images.*

*Python-tesseract is a wrapper for Google’s Tesseract-OCR Engine. It is also useful as a stand-alone invocation script to tesseract, as it can read all image types supported by the Pillow and Leptonica imaging libraries, including jpeg, png, gif, bmp, tiff, and others.*

[https://pypi.org/project/pytesseract/](https://pypi.org/project/pytesseract/)

Once finished processing, the tool will return the subtitles to be stored in the following file:

    <TEXT_STORE>/subtitles/<video_id>.srt

### Transcription-Subtitle Alignment

The tool should have an interface to compare video and text files (audio transcripts and extracted subtitles) side by side, allowing users to review, search and make the necessary edits and corrections of any errors.

This "validation interface" should have a simple UI, consisting mainly of a video viewer with playback controls (such as "play" and "stop") where users will be able to verify the temporally aligning of video and transcription (similar to YouTube's auto-generated subtitles interface).

After reviewing, the tool should merge both audio transcripts and extracted subtitles (aligned and synced according to their timestamps) into one single file, then output to a combined folder.

    <TEXT_STORE>/combined/<video_id>.json

### Item Exporter

Once we have all of the above, we want to export those items to the webtool. For this purpose, the pipeline should provide different output formats, such as XML, CSV or JSON.

---

Some links for reference:

[http://www.redhenlab.org/home/tutorials-and-educational-resources/-red-hen-rapid-annotator](http://www.redhenlab.org/home/tutorials-and-educational-resources/-red-hen-rapid-annotator)

[https://sites.google.com/case.edu/techne-public-site/red-hen-rapid-annotator](https://sites.google.com/case.edu/techne-public-site/red-hen-rapid-annotator)


