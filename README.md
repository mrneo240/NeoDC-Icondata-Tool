# VMU Icon generator:
NeoDC, 2017.

License:
Respect and dont steal.

This tool will enable you to take a 32x32 image and 
then transform it into a suitable format to use on a VMU

Little to no error checking is done, no safety is implied.
NeoDC is not responsible for anything that happens with this.

Requirements:
Webserver+PHP (min 5.2.17.17 i guess, thats what i use)
access to files, upload functionality isnt present at this time
Web Browser

How to use:
STEP 1 - Create the ICONDATA.VMS
browse to color_extract.php (working title)
- Ignore errors most likely
- Configure the page: with arguments POST/GET
    - img=king.png      (the image to work from, must be 32x32)
    - invert=1/0        (should the B&W icon be inverted) OPTIONAL
    - threshold=0-FF    (threshold for visible pixel in B&W image) OPTIONAL
- Ex: color_extract.php?img=flower.png&threshold=55&invert=1

The page will show you:
- The palette it generated (up to 16 colors)
- The B&W icon it generated
- Your original image

Congrats! ICONDATA.VMS has now been created!
- Verify the create image with vms_icondata.php
- This will read your newly created file and display how the dreamcast will

STEP 2 - Create a valid ICONDATA.VMI file (for vmu tools, planetweb browser, etc...)
browser to vmi_format.php (working title)
- Page will be blank (its ok!)
- use commands to get stuff done
- Commands:
    - cmd=NONE      (nothing)
    - cmd=createVMI (write .VMI with optional parameters)
        - desc=Game_Save    (the description for the file, up to 32 characters) OPTIONAL
        - cpy=NeoDC         (copyright holder, up to 32 characters) OPTIONAL
        - name=SONIC        (filename to write)
        - Ex: vmi_format.php?cmd=writeVMI_ICON&desc=TESTING&cpy=VIETNAM&name=SONIC
    - cmd=writeVMI_ICON (write ICONDATA.VMI with optional parameters)
        - desc=Game_Save    (the description for the file, up to 32 characters) OPTIONAL
        - cpy=NeoDC         (copyright holder, up to 32 characters) OPTIONAL
        - Ex: vmi_format.php?cmd=writeVMI_ICON&desc=TESTING&cpy=VIETNAM
    - cmd=readVMI (read and display information about a .VMI)
        - name=SONIC        (filename to read, becomes [NAME].VMI)
        - Ex: vmi_format.php?cmd=readVMI&name=SONIC
        
CONGRATS!
You are now able to use your .VMI and .VMS whereever you would like