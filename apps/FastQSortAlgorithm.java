public class FastQSortAlgorithm extends SortAlgorithm 
{
   /** This is a generic version of C.A.R Hoare's Quick Sort 
   * algorithm.  This will handle arrays that are already
   * sorted, and arrays with duplicate keys.<BR>
   *
   * If you think of a one dimensional array as going from
   * the lowest index on the left to the highest index on the right
   * then the parameters to this function are lowest index or
   * left and highest index or right.  The first time you call
   * this function it will be with the parameters 0, a.length - 1.
   *
   * @param a         an integer array
   * @param lo0     left boundary of array partition
   * @param hi0     right boundary of array partition
   */
   private void QuickSort(int a[], int l, int r) throws Exception
   {
        int M = 4;
        int i;
        int j;
        int v;

        if ((r-l)>M)
        {
                i = (r+l)/2;
                if (a[l]>a[i]) swap(a,l,i);     // Tri-Median Methode!
                if (a[l]>a[r]) swap(a,l,r);
                if (a[i]>a[r]) swap(a,i,r);

                j = r-1;
                swap(a,i,j);
                i = l;
                v = a[j];
                for(;;)
                {
                        while(a[++i]<v);
                        while(a[--j]>v);
                        if (j<i) break;
                        swap (a,i,j);
//                        pause(i,j);
//                        if (stopRequested) {
//                            return;
//                        }
                }
                swap(a,i,r-1);
//                pause(i);
                QuickSort(a,l,j);
                QuickSort(a,i+1,r);
        }
    }

    private void swap(int a[], int i, int j)
    {
            int T;
            T = a[i]; 
            a[i] = a[j];
            a[j] = T;
    }
    
    private void InsertionSort(int a[], int lo0, int hi0) throws Exception
    {
            int i;
            int j;
            int v;
    
            for (i=lo0+1;i<=hi0;i++)
            {
                    v = a[i];
                    j=i;
                    while ((j>lo0) && (a[j-1]>v))
                    {
                            a[j] = a[j-1];
//                            pause(i,j);
                            j--;
                    }
                    a[j] = v;
            }
    }
    
    public void sort(int a[]) throws Exception
    {
            QuickSort(a, 0, a.length - 1);
            InsertionSort(a,0,a.length-1);
//            pause(-1,-1);
    }
}
