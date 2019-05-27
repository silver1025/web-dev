## GitHub多人协作开发

> By 屈仟

#### 准备工作

> 示例中 quqia 账号为组长， silver1025 账号为组员，仓库名为 test

1. 组员 fork 组长的相应的仓库

   <img src="img\github-test\github-test-01.png" />

2. 组长进入相应的仓库，在 settings-> collaborators  添加组员为 collaborator

   <img src="img\github-test\github-test-02.png" />

3. 组员此时登陆自己 GitHub 账号目前的主邮箱，查看相应的邀请邮件，并同意邀请

   <img src="img\github-test\github-test-03.png" width="70%" />

<img src="img\github-test\github-test-04.png" width="70%"/>

4. 组员在本地 clone 远程自己 fork 的仓库

   

   <img src="img\github-test\github-test-05.png" width="70%"/>

5. 组员进入刚刚 clone 的仓库的文件夹，添加组长的仓库为远程仓库

   ```
   git remote add upstream git@github.com:quqia/test.git
   ```

   查看添加结果

   ```
   git remote -v
   ```

   <img src="img\github-test\github-test-06.png" width="70%"/>

#### 提交代码到自己的远程仓库

1. 组员在本地编辑完代码后，commit 到本地仓库

   > 在这里以在 README.md 中添加内容为例

   ```
    echo "silver1025 edited by silver1025">>README.md
    git add .
    git commit -m"silver1025 first commit"
   ```

   在示例中我往 README.md 中添加了一行文字  "silver1025 edited by silver1025"

<img src="img\github-test\github-test-07.png" width="70%"/>

2. 抓取组长仓库的修改

   ```
   git fetch upstream
   ```

   <img src="img\github-test\github-test-08.png" width="70%"/>

3. 使用 rebase 将本地 mater 分支与组长的 upstream/master 分支合并（变基合并）

   > 为演示遇到冲突的情况，此时组长的远程仓库里的README.md已经被修改，
   >
   > 添加了一行文字  "quqia edited by quqia" ，必然会导致冲突

   ```
   git rebase upstream/master
   ```

   <img src="img\github-test\github-test-09.png" width="70%"/>

   此时提示我们遇到了冲突，先手动解决冲突，再运行```git rebase --continue```

4. 解决冲突

   打开冲突的文件 ```vim README.md``` (使用任何编辑器都可以)

   <img src="img\github-test\github-test-10.png" width="70%"/>

   Git用 `<<<<<<<`，`=======`，`>>>>>>>` 标记出不同分支的内容，我们修改如下后保存：

   <img src="img\github-test\github-test-11.png" width="70%"/>

   然后继续 rebase

   > 修改完之后要先将文件 add 至缓冲区，截图中演示了不 add 时会报错

   ```
   git add .
   git rebase --continue
   ```

   <img src="img\github-test\github-test-12.png" width="70%"/>

   用带参数的 `git log `也可以看到分支的合并情况：

   ```
   git log --graph --pretty=oneline --abbrev-commit
   ```

   <img src="img\github-test\github-test-13.png" width="70%"/>

   可以发现没有像直接 merge 那样，分支先分叉再合并，而是一条直线，这便是 rebase 的美妙之处了

5. push 到自己的远程库

   ```
   git push origin master
   ```

   <img src="img\github-test\github-test-14.png" width="70%"/>

#### Pull Request

1. 到 GitHub 自己的仓库新建 Pull Request

   <img src="img\github-test\github-test-15.png" />

2. 点击后进入如下页面，点击 create pull request，然后添加 comment 提交

   <img src="img\github-test\github-test-16.png"/>

3. 由于组长已经把组员添加到了 collaborator 列表中，所以我们拥有组长这个仓库的 push 权限，点击 merge pull request，我们的代码便合并到组长的仓库了。

   <img src="img\github-test\github-test-17.png"/>

4. 查看 commit 记录，可以发现 rebase 不会产生额外的 commit 记录，而使用 merge 则会

   > commit 记录中发现，使用 quqia 账号的产生 commit 记录，也显示为 silver1025 产生的，后来我又用 quqia 账号提交了一个 commit，发现还是标注是 silver1025 产生的，分析后发现是因为提交记录时，user.email 填写的是 silver1025 的账号主邮箱

   <img src="img\github-test\github-test-19.png"/>

#### 添加分支保护后的 Pull Request

1. 组长添加分支保护规则，要求合并前至少有另一个 组内成员 review 后 approve

   <img src="img\github-test\github-test-20.png"/>

   、<img src="img\github-test\github-test-21.png"/>

   <img src="img\github-test\github-test-22.png"/>

2. 组员像之前那样提交 Pull Request，comment 里面至少 @ 一个组内成员，现在即使有 push 权限，也不能直接合并了。

   <img src="img\github-test\github-test-23.png"/>

   <img src="img\github-test\github-test-24.png"/>

3. 被 @ 的另外一个组内成员的邮箱会收到相关邮件，该成员来 review 代码

   > 由于演示时只有两个账号, @ 时 @ 了组长, 实际操作时应该减轻组长 review 的压力, 每次由不同的人来 review

   <img src="img\github-test\github-test-26.png" width="70%"/>

   <img src="img\github-test\github-test-27.png"/>

4. 添加 review 时有三个选项，区别大家可以阅读下面的描述。

   > 为了演示 Pull Request 需要二次更改的情况，我添加了一个需要更改的 review，如果 review 时选择第二个选项直接通过，则 Pull Request 发起者可以直接合并了。

   review 代码的组内成员先添加一个普通的反馈。

   <img src="img\github-test\github-test-28.png"/>

   此次 Pull Request 发起者的邮箱会收到如下的邮件，此时 Pull Request 并没有被通过。

   <img src="img\github-test\github-test-29.png" width="70%"/>

   review 代码的组内成员再添加一个需要更改的 review。

   <img src="img\github-test\github-test-30.png"/>

   此次 Pull Request 发起者的邮箱会收到如下的邮件，此时 Pull Request 还是没有被通过。

   <img src="img\github-test\github-test-31.png" width="70%"/>

5. Pull Request 发起者根据要求更改，然后直接 push 到我们自己 fork 的仓库分支上就可以了，GitHub会自动给review 代码的组内成员发邮件。完成修改要求后，Pull Request 发起者可以点击红框内的 Re-request review，GitHub会再给review 代码的组内成员发邮件。

   <img src="img\github-test\github-test-32.png"/>

   <img src="img\github-test\github-test-35.png" width="70%"/>

   review 代码的组内成员会收到如下邮件。

   <img src="img\github-test\github-test-34.png" width="70%"/>

   <img src="img\github-test\github-test-33.png" width="70%"/>

6. review 代码的组内成员如果 approve 此次修改，Pull Request 发起者的邮箱会收到相应邮件，此时就可以合并分支了。

   <img src="img\github-test\github-test-36.png" width="70%"/>

   <img src="img\github-test\github-test-37.png"/>

   合并时我们可以在右侧下拉框选择 rebase ，可以发现不会产生新的 commit 记录。

   <img src="img\github-test\github-test-38.png"/>

#### 总结

其实总体的过程和我们在华为云上的差不多，只是 pull 换成了 fetch + rebase，而且 PR 是从仓库的副本发起，而不是从一个分支发起。

添加了分支保护策略之后，即使有 push 权限，也不能直接合并了，需要得到相应人数的 review approve才能合并。如果 review 的人提出了修改要求，修改后直接 push 到我们自己 fork 的仓库的分支上就可以了，GitHub知道你这是为了这次 PR 做的修改。

我也才学 git 没多久，如果其中有写的不对的地方，欢迎大家批评指正。

#### 参考资料

<a href="https://git-scm.com/book/zh/v2/">Pro Git book</a>

[廖雪峰的git教程](https://www.liaoxuefeng.com/wiki/0013739516305929606dd18361248578c67b8067c8c017b000)

[使用git fetch和git rebase处理多人开发同一分支的问题](https://blog.csdn.net/azureternite/article/details/76154807)

[详解git pull和git fetch的区别](https://blog.csdn.net/weixin_41975655/article/details/82887273)

[Defining the mergeability of pull requests](<https://help.github.com/en/articles/defining-the-mergeability-of-pull-requests>)

<a href="https://help.github.com/en/articles/approving-a-pull-request-with-required-reviews">Approving a pull request with required reviews</a>

