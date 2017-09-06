如何建立自己的Line骰子機器人
==

準備動作：
--
先申請好Line帳號（廢話）
先申請好Github帳號
先申請好Heroku帳號
以下全部選擇用免費的服務就夠了，請不要手殘選到付費。


Step1：先把這個專案Fork回去
--
看到右上角的 ![Fork](http://i.imgur.com/g5VmzkC.jpg) 按鈕嗎，按下去。
把這個專案存到你的Github裡。


Step2：建立lineBot賬號
--
到[https://business.line.me/zh-hant/companies/1253547/services/bot](https://business.line.me/zh-hant/companies/1253547/services/bot)申請一個帳號，
![開始使用Messaging API](http://i.imgur.com/Zb2Oboy.jpg)
點選「開始使用Messaging API」，按照指示建立一個line賬號。

當你看到這個畫面的時候，點右邊那個「前往LINE@MANAGER」
![前往LINE@MANAGER](http://i.imgur.com/C2mzamX.jpg)

你會看到這個，總之還是點下去
![警告](http://i.imgur.com/XfRa9UU.jpg)

照著這個畫面設定
![設定](http://i.imgur.com/PXf10Qs.jpg)

接下來移到上面，看到「LINE Developers」了嗎？按下去，然後開著網頁不要關。
![LINE Developers](http://i.imgur.com/aks55p4.jpg)


Step3：將LineBot部署到Heroku
--

按一下下面這個按鈕
[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

你會看到這個
![Heroku](http://i.imgur.com/sbCVOcW.jpg)

當然，先取一個App name，那底下兩個要在哪裡找呢，回到上個步驟的LINE Developers網頁


Step4：取得Channel Access Token和Channel Secret
--
先取得Channel Secret，按右邊的按鈕
![Channel Secret](http://i.imgur.com/oNN9gUx.jpg)
把取得的字串複製到Step3的LINE_CHANNEL_SECRET

再取得Channel Access Token，按右邊的按鈕
![Channel Access Token](http://i.imgur.com/UJ4AQlJ.jpg)
把取得的字串複製到Step3的LINE_CHANNEL_ACCESSTOKEN

接著，按下Deploy app，等他跑完之後按下Manage App
距離部署完機器人只差一步啦！


Step4：鏈接Line與Heroku
--
點選settings
![setting](http://i.imgur.com/9fEMoVh.jpg)

找到Domains and certificates這個條目，旁邊會有個「Your app can be found at……」加一串網址，把網址複製起來。
![Domain](http://i.imgur.com/dcgyeZa.jpg)

回到LINE Developers網頁，選取最底下的edit，找到Webhook URL，把那串網址複製上去，尾巴加上 /LINE/
![webhook](http://i.imgur.com/tn2EN6l.jpg)

按下Save。看到在 Webhook URL 旁邊有個 VERIFY 按鈕嗎，按下去。
如果出現 Success. 就表示你成功完成啦！
![Success](http://i.imgur.com/yjlpIh8.jpg)


