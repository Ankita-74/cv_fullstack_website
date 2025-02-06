c language


# include<stdio.h>
# include<conio.h>
# include<string.h>
struct student
{
    int RNO;
    char name[10];
    float per;
    int m1,m2,m3,sum;
}
s1;
void main()
{
    clrscr();
    s1,RNO=1;
    strcpy(s1,name,"xyz");
    s1.m1=50;
    s1.m2=55;
    s1.m3=65;

    s1.sum=s1.m1+s1.m2+s1.m3;
    s1.per=(s1.sum/3);

    printf("RNO=%d",s1.RNO);
    printf("name=%s",s1.name);
    printf("m1=%d  m2=%d m3=%d",s1.m1,s1.m2,s1.m3); 
    printf("m1=%d m2=%d m3=%d ",s1.m1 s1.m2 s1.m3);
    printf("sum=%d",s1.sum);
    printf("percentage =%f",s1.per);
    printf("size of strcture =%d",size of(s1));
    getch();
}