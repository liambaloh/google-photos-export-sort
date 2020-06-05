# google-photos-export-sort
This PHP script sorts the content from Google Photos when exported via the Google Takeout feature

# How to Use:

## Prerequisites

- Install [XAMPP](https://www.apachefriends.org/index.html) or your OS' equivalent
- Open `C:/XAMPP/php/php.ini` and change `max_execution_time=300000`
- Create a folder called `sortphotos` in C:/XAMPP/htdocs/ or your OS' equivalent
- Start Apache in XAMPP's control panel

## Downloading and Grouping Photos

- Create an export using [Google Takeout](https://takeout.google.com/settings/takeout)
  - Only Google Photos takeout is required
  - Select only albums which are formatted as dates, all others correspond to albums and will result in duplicates and a much larger download size, unless you want to preserve album structures
- It takes a while for the download to be ready (hours/days) - you'll get an email
- Download the data takeout when it's ready - it will be split into many .zip files. Download and extract them all
- Create a folder called `Grouped` and put the contents of each zip into it. Grouped should thus contain a lot of folders which correspond to dates and album names. This process of grouping shouldn't result in any files being overwritten.

## Sorting Photos

- Save index.php to `C:/XAMPP/htdocs/sortphotos/index.php` or your OS' equivalent
- Move the `Grouped` folder to `C:/XAMPP/htdocs/sortphotos/Grouped`
- Open index.php and modify the value of `$folder_in` to point to the location where the list of folders which correspond to albums and dates are (if you've been following this guide precisely, it'll probably be `"./Grouped/Takeout/Google Photos/"`
- If you want to TEST this software on a small batch of files, 
  - Uncomment the lines 
  ```
      //if($dateFolder != "__trial"){
      //    continue;
      //}
  ```
  - Make a folder called `__trial` among the data and album folders
  - Copy some stuff from other folders into this folder
  - Open [localhost/sortphotos](http://localhost/sortphotos/) in your browser, wait for it to process
  - Look at the `__trial` folder again and see what happened. You should see the content sorted and renamed when appropriate.
- Open [localhost/sortphotos](http://localhost/sortphotos/) in your browser, wait for it to process (will take a few minutes)
- Your photos and now sorted within each folder, files are renamed, extensions changed, etc. ready for further processing.
